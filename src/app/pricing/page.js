'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import Loader from '@/components/Loader';

/**
 * Razorpay Payment Integration
 */
const initiateRazorpayPayment = async (plan, token, onSuccess, onFailure) => {
  try {
    if (!token) {
      throw new Error('Please login to continue');
    }

    // Create order
    const orderResponse = await fetch(`${baseUrl}/api/subscription/create-order`, {
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
          const verifyResponse = await fetch(`${baseUrl}/api/subscription/verify-payment`, {
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
      theme: {
        color: '#126E97',
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
 * Plans/Pricing Page Component
 */
export default function PlansPage() {
  const [plans, setPlans] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [processing, setProcessing] = useState(false);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const router = useRouter();

  useEffect(() => {
    const userToken = Cookies.get('user-token');
    if (userToken) {
      setIsAuthenticated(true);
      fetchPlans(userToken);
    } else {
      setIsAuthenticated(false);
      setLoading(false);
    }
  }, []);

  const fetchPlans = async (token) => {
    try {
      const response = await fetch(`${baseUrl}/api/subscription/plans`, {
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
      console.error('Error fetching plans:', error);
    } finally {
      setLoading(false);
    }
  };

  const handlePurchase = async (plan) => {
    const token = Cookies.get('user-token');

    if (!token) {
      toast.error('Please login to purchase a plan');
      setTimeout(() => {
        const myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
      }, 500);
      return;
    }

    setProcessing(true);

    await initiateRazorpayPayment(
      plan,
      token,
      (subscription) => {
        setProcessing(false);
        toast.success(`Successfully subscribed to ${plan.name}!`);
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      },
      (error) => {
        setProcessing(false);
        toast.error(`Payment failed: ${error}`);
      }
    );
  };

  const showLoginModal = () => {
    const myModal = new bootstrap.Modal(document.getElementById('myModal'));
    myModal.show();
  };

  return (
    <>
      <Navbar />
      <ToastContainer position="top-right" autoClose={3000} />
      
      <div className="main-wrapper">
        <section style={{ minHeight: '80vh' }}>
          <div className="container">
            {/* Page Header */}
            <div className="text-center mb-5">
              <h1 className="text-white mb-3">Choose Your Plan</h1>
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '18px' }}>
                Select the perfect plan for your radiology exam preparation
              </p>
            </div>

            {loading ? (
              <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
                <Loader />
              </div>
            ) : error ? (
              <div className="text-center" style={{ minHeight: '400px', paddingTop: '100px' }}>
                <p className="text-danger mb-4">{error}</p>
                <button 
                  onClick={() => {
                    const token = Cookies.get('user-token');
                    if (token) fetchPlans(token);
                  }} 
                  className="loginBtn"
                >
                  Retry
                </button>
              </div>
            ) : !isAuthenticated ? (
              <div className="text-center" style={{ minHeight: '400px', paddingTop: '100px' }}>
                <h3 className="text-white mb-4">Please Login to View Plans</h3>
                <button onClick={showLoginModal} className="loginBtn">
                  Login Now
                </button>
              </div>
            ) : plans.length === 0 ? (
              <div className="text-center" style={{ minHeight: '400px', paddingTop: '100px' }}>
                <h3 className="text-white">No Plans Available</h3>
                <p style={{ color: 'rgba(255, 255, 255, 0.60)' }}>Please check back later</p>
              </div>
            ) : (
              <div className="row justify-content-center">
                {plans.map((plan) => (
                  <div key={plan.id} className="col-lg-4 col-md-6 mb-4">
                    <div 
                      className="pricing-card" 
                      style={{
                        background: plan.is_featured ? 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)' : '#282D41',
                        borderRadius: '15px',
                        padding: '30px',
                        border: plan.is_featured ? '2px solid #126E97' : '1px solid rgba(255, 255, 255, 0.1)',
                        transform: plan.is_featured ? 'scale(1.05)' : 'scale(1)',
                        transition: 'all 0.3s ease',
                        position: 'relative',
                        height: '100%',
                      }}
                    >
                      {plan.is_featured && (
                        <div 
                          style={{
                            position: 'absolute',
                            top: '-15px',
                            left: '50%',
                            transform: 'translateX(-50%)',
                            background: '#FFA500',
                            color: '#fff',
                            padding: '5px 20px',
                            borderRadius: '20px',
                            fontSize: '14px',
                            fontWeight: '600',
                          }}
                        >
                          MOST POPULAR
                        </div>
                      )}

                      <div className="text-center mb-4">
                        <h3 className="text-white mb-2" style={{ fontSize: '24px', fontWeight: '700' }}>
                          {plan.name}
                        </h3>
                        <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px', minHeight: '40px' }}>
                          {plan.description}
                        </p>
                      </div>

                      <div className="text-center mb-4">
                        <div className="d-flex align-items-baseline justify-content-center">
                          <span className="text-white" style={{ fontSize: '42px', fontWeight: '700' }}>
                            ₹{plan.effective_price}
                          </span>
                          <span style={{ color: 'rgba(255, 255, 255, 0.60)', marginLeft: '10px' }}>
                            / {plan.duration_text}
                          </span>
                        </div>
                        {plan.discount_price && plan.discount_price > 0 && (
                          <p style={{ color: 'rgba(255, 255, 255, 0.50)', textDecoration: 'line-through', fontSize: '16px' }}>
                            ₹{plan.price}
                          </p>
                        )}
                      </div>

                      <div className="mb-4">
                        <h5 className="text-white mb-3" style={{ fontSize: '16px', fontWeight: '600' }}>
                          Includes Access To:
                        </h5>
                        <ul className="list-unstyled">
                          {plan.modules.map((module) => (
                            <li key={module.id} className="mb-2 d-flex align-items-center">
                              <i className={`${module.icon || 'fas fa-check-circle'} me-2`} style={{ color: '#FFA500' }}></i>
                              <span style={{ color: 'rgba(255, 255, 255, 0.80)' }}>{module.name}</span>
                            </li>
                          ))}
                        </ul>
                      </div>

                      {plan.features && plan.features.length > 0 && (
                        <div className="mb-4">
                          <h5 className="text-white mb-3" style={{ fontSize: '16px', fontWeight: '600' }}>
                            Features:
                          </h5>
                          <ul className="list-unstyled">
                            {plan.features.map((feature, index) => (
                              <li key={index} className="mb-2 d-flex align-items-start">
                                <i className="fas fa-check me-2 mt-1" style={{ color: '#4CAF50' }}></i>
                                <span style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px' }}>{feature}</span>
                              </li>
                            ))}
                          </ul>
                        </div>
                      )}

                      <button
                        onClick={() => handlePurchase(plan)}
                        disabled={processing}
                        className="w-100"
                        style={{
                          background: plan.is_featured ? '#FFA500' : '#126E97',
                          color: '#fff',
                          border: 'none',
                          padding: '15px',
                          borderRadius: '8px',
                          fontSize: '16px',
                          fontWeight: '600',
                          cursor: processing ? 'not-allowed' : 'pointer',
                          opacity: processing ? 0.6 : 1,
                          transition: 'all 0.3s ease',
                        }}
                        onMouseEnter={(e) => {
                          if (!processing) {
                            e.target.style.transform = 'translateY(-2px)';
                            e.target.style.boxShadow = '0 5px 15px rgba(18, 110, 151, 0.4)';
                          }
                        }}
                        onMouseLeave={(e) => {
                          e.target.style.transform = 'translateY(0)';
                          e.target.style.boxShadow = 'none';
                        }}
                      >
                        {processing ? 'Processing...' : 'Subscribe Now'}
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {/* Additional Info */}
            {isAuthenticated && plans.length > 0 && (
              <div className="row mt-5">
                <div className="col-12 text-center">
                  <div style={{
                    background: '#282D41',
                    borderRadius: '10px',
                    padding: '30px',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                  }}>
                    <h4 className="text-white mb-3">Why Choose DrOutlier?</h4>
                    <div className="row">
                      <div className="col-md-4 mb-3">
                        <i className="fas fa-graduation-cap mb-2" style={{ fontSize: '32px', color: '#126E97' }}></i>
                        <h6 className="text-white">Expert Content</h6>
                        <p style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                          Curated by radiology experts
                        </p>
                      </div>
                      <div className="col-md-4 mb-3">
                        <i className="fas fa-mobile-alt mb-2" style={{ fontSize: '32px', color: '#126E97' }}></i>
                        <h6 className="text-white">Learn Anywhere</h6>
                        <p style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                          Access on any device, anytime
                        </p>
                      </div>
                      <div className="col-md-4 mb-3">
                        <i className="fas fa-trophy mb-2" style={{ fontSize: '32px', color: '#126E97' }}></i>
                        <h6 className="text-white">Exam Success</h6>
                        <p style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                          Proven track record of success
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </section>
      </div>

      <Footer />
    </>
  );
}
