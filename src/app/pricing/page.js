'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

/**
 * Razorpay Payment Integration
 * @param {object} plan - The selected plan object
 * @param {function} onSuccess - Callback function on successful payment
 * @param {function} onFailure - Callback function on payment failure
 */
export const initiateRazorpayPayment = async (plan, onSuccess, onFailure) => {
  try {
    const token = localStorage.getItem('userToken');
    
    if (!token) {
      throw new Error('Please login to continue');
    }

    // Create order
    const orderResponse = await fetch('https://admin.droutlier.com/api/subscription/create-order', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ plan_id: plan.id }),
    });

    const orderData = await orderResponse.json();

    if (orderData.status !== 'success') {
      throw new Error(orderData.message?.error?.[0] || 'Failed to create order');
    }

    // Load Razorpay script if not already loaded
    if (!window.Razorpay) {
      const script = document.createElement('script');
      script.src = 'https://checkout.razorpay.com/v1/checkout.js';
      script.async = true;
      document.body.appendChild(script);
      
      await new Promise((resolve) => {
        script.onload = resolve;
      });
    }

    const options = {
      key: orderData.data.razorpay_key,
      amount: orderData.data.amount,
      currency: orderData.data.currency,
      name: 'DrOutlier',
      description: `${plan.name} Subscription`,
      order_id: orderData.data.order_id,
      handler: async function (response) {
        try {
          // Verify payment
          const verifyResponse = await fetch('https://admin.droutlier.com/api/subscription/verify-payment', {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              razorpay_order_id: response.razorpay_order_id,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_signature: response.razorpay_signature,
              subscription_id: orderData.data.subscription_id,
            }),
          });

          const verifyData = await verifyResponse.json();

          if (verifyData.status === 'success') {
            onSuccess(verifyData.data.subscription);
          } else {
            onFailure(verifyData.message?.error?.[0] || 'Payment verification failed');
          }
        } catch (error) {
          onFailure(error.message || 'Payment verification failed');
        }
      },
      prefill: {
        email: localStorage.getItem('userEmail') || '',
        contact: localStorage.getItem('userPhone') || '',
      },
      theme: {
        color: '#3B82F6',
      },
      modal: {
        ondismiss: function() {
          onFailure('Payment cancelled');
        }
      }
    };

    const razorpay = new window.Razorpay(options);
    razorpay.open();
  } catch (error) {
    onFailure(error.message || 'Failed to initiate payment');
  }
};

/**
 * Plans List Component
 */
export default function PlansPage() {
  const [plans, setPlans] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [processing, setProcessing] = useState(false);
  const router = useRouter();

  useEffect(() => {
    fetchPlans();
  }, []);

  const fetchPlans = async () => {
    try {
      const token = localStorage.getItem('userToken');
      
      if (!token) {
        router.push('/login');
        return;
      }

      const response = await fetch('https://admin.droutlier.com/api/subscription/plans', {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      const data = await response.json();

      if (data.status === 'success') {
        setPlans(data.data.plans);
      } else {
        setError('Failed to load plans');
      }
    } catch (error) {
      setError('Failed to load plans');
    } finally {
      setLoading(false);
    }
  };

  const handlePurchase = async (plan) => {
    setProcessing(true);

    await initiateRazorpayPayment(
      plan,
      (subscription) => {
        setProcessing(false);
        // Show success message
        alert(`Successfully subscribed to ${plan.name}!`);
        // Redirect to dashboard or subscription page
        router.push('/dashboard');
      },
      (error) => {
        setProcessing(false);
        alert(`Payment failed: ${error}`);
      }
    );
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-center">
          <p className="text-red-500">{error}</p>
          <button onClick={fetchPlans} className="mt-4 btn btn-primary">
            Retry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="text-center mb-12">
        <h1 className="text-4xl font-bold mb-4">Choose Your Plan</h1>
        <p className="text-gray-600">Select the perfect plan for your learning journey</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {plans.map((plan) => (
          <div
            key={plan.id}
            className={`rounded-lg shadow-lg p-6 ${
              plan.is_featured ? 'border-2 border-blue-500 transform scale-105' : 'border border-gray-200'
            }`}
          >
            {plan.is_featured && (
              <div className="text-center mb-2">
                <span className="inline-block bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                  Most Popular
                </span>
              </div>
            )}

            <h2 className="text-2xl font-bold mb-2">{plan.name}</h2>
            <p className="text-gray-600 mb-4">{plan.description}</p>

            <div className="mb-6">
              <div className="flex items-baseline">
                <span className="text-4xl font-bold">₹{plan.effective_price}</span>
                <span className="text-gray-500 ml-2">/ {plan.duration_text}</span>
              </div>
              {plan.discount_price && (
                <p className="text-sm text-gray-500 line-through">₹{plan.price}</p>
              )}
            </div>

            <div className="mb-6">
              <h3 className="font-semibold mb-2">Includes Access To:</h3>
              <ul className="space-y-2">
                {plan.modules.map((module) => (
                  <li key={module.id} className="flex items-center">
                    <i className={`${module.icon} mr-2 text-blue-500`}></i>
                    <span>{module.name}</span>
                  </li>
                ))}
              </ul>
            </div>

            {plan.features && plan.features.length > 0 && (
              <div className="mb-6">
                <h3 className="font-semibold mb-2">Features:</h3>
                <ul className="space-y-2">
                  {plan.features.map((feature, index) => (
                    <li key={index} className="flex items-start">
                      <i className="fas fa-check text-green-500 mr-2 mt-1"></i>
                      <span className="text-sm">{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}

            <button
              onClick={() => handlePurchase(plan)}
              disabled={processing}
              className={`w-full py-3 px-6 rounded-lg font-semibold transition ${
                plan.is_featured
                  ? 'bg-blue-500 hover:bg-blue-600 text-white'
                  : 'bg-gray-200 hover:bg-gray-300 text-gray-800'
              } ${processing ? 'opacity-50 cursor-not-allowed' : ''}`}
            >
              {processing ? 'Processing...' : 'Subscribe Now'}
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}
