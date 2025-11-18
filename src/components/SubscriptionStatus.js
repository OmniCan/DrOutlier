'use client';

import { useEffect, useState } from 'react';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';
import Link from 'next/link';

/**
 * Component to display user's subscription status
 * Can be used in profile, dashboard, or anywhere to show subscription info
 */
export default function SubscriptionStatus() {
  const [subscription, setSubscription] = useState(null);
  const [loading, setLoading] = useState(true);
  const [hasSubscription, setHasSubscription] = useState(false);

  useEffect(() => {
    fetchSubscription();
  }, []);

  const fetchSubscription = async () => {
    try {
      const token = Cookies.get('user-token');
      
      if (!token) {
        setLoading(false);
        return;
      }

      const response = await fetch(`${baseUrl}/api/subscription/my-subscription`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      const data = await response.json();

      if (data.status === 'success' && data.data.has_subscription) {
        setSubscription(data.data.subscription);
        setHasSubscription(true);
      } else {
        setHasSubscription(false);
      }
    } catch (error) {
      console.error('Error fetching subscription:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="subscription-status-card" style={{
        background: '#282D41',
        borderRadius: '10px',
        padding: '20px',
        border: '1px solid rgba(255, 255, 255, 0.1)',
      }}>
        <p style={{ color: 'rgba(255, 255, 255, 0.60)', margin: 0 }}>Loading...</p>
      </div>
    );
  }

  if (!hasSubscription) {
    return (
      <div className="subscription-status-card" style={{
        background: 'linear-gradient(135deg, #282D41 0%, #1B1E27 100%)',
        borderRadius: '10px',
        padding: '25px',
        border: '1px solid rgba(255, 255, 255, 0.1)',
      }}>
        <div className="d-flex align-items-center justify-content-between">
          <div>
            <h6 className="text-white mb-2">No Active Subscription</h6>
            <p style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px', margin: 0 }}>
              Subscribe now to access premium content
            </p>
          </div>
          <Link href="/pricing" className="loginBtn" style={{ whiteSpace: 'nowrap' }}>
            View Plans
          </Link>
        </div>
      </div>
    );
  }

  // Calculate status color
  const daysRemaining = subscription.days_remaining;
  const statusColor = daysRemaining > 30 ? '#4CAF50' : daysRemaining > 7 ? '#FFA500' : '#FF5252';

  return (
    <div className="subscription-status-card" style={{
      background: 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)',
      borderRadius: '10px',
      padding: '25px',
      border: '2px solid #126E97',
    }}>
      <div className="row align-items-center">
        <div className="col-md-8">
          <div className="d-flex align-items-center mb-3">
            <i className="fas fa-crown me-2" style={{ color: '#FFA500', fontSize: '24px' }}></i>
            <div>
              <h5 className="text-white mb-0" style={{ fontSize: '18px', fontWeight: '600' }}>
                {subscription.plan.name}
              </h5>
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px', margin: 0 }}>
                Active Subscription
              </p>
            </div>
          </div>
          
          <div className="row">
            <div className="col-6 mb-2">
              <small style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '12px' }}>
                Started
              </small>
              <p className="text-white mb-0" style={{ fontSize: '14px' }}>
                {new Date(subscription.started_at).toLocaleDateString('en-GB')}
              </p>
            </div>
            <div className="col-6 mb-2">
              <small style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '12px' }}>
                Expires
              </small>
              <p className="text-white mb-0" style={{ fontSize: '14px' }}>
                {new Date(subscription.expires_at).toLocaleDateString('en-GB')}
              </p>
            </div>
          </div>

          <div className="mt-3">
            <div className="d-flex align-items-center">
              <div className="flex-grow-1">
                <div style={{
                  background: 'rgba(255, 255, 255, 0.2)',
                  borderRadius: '10px',
                  height: '8px',
                  overflow: 'hidden',
                }}>
                  <div style={{
                    background: statusColor,
                    width: `${Math.min((daysRemaining / 365) * 100, 100)}%`,
                    height: '100%',
                    borderRadius: '10px',
                    transition: 'width 0.3s ease',
                  }}></div>
                </div>
              </div>
              <span className="ms-3 text-white" style={{ fontSize: '14px', fontWeight: '600' }}>
                {daysRemaining} days left
              </span>
            </div>
          </div>
        </div>

        <div className="col-md-4 mt-3 mt-md-0">
          <div style={{
            background: 'rgba(255, 255, 255, 0.1)',
            borderRadius: '8px',
            padding: '15px',
          }}>
            <h6 className="text-white mb-2" style={{ fontSize: '14px' }}>Access to:</h6>
            <ul className="list-unstyled mb-0">
              {subscription.modules.slice(0, 4).map((module, index) => (
                <li key={index} className="mb-1" style={{ fontSize: '13px', color: 'rgba(255, 255, 255, 0.80)' }}>
                  <i className={`${module.icon || 'fas fa-check'} me-2`} style={{ color: '#FFA500', fontSize: '12px' }}></i>
                  {module.name}
                </li>
              ))}
              {subscription.modules.length > 4 && (
                <li style={{ fontSize: '13px', color: 'rgba(255, 255, 255, 0.60)' }}>
                  +{subscription.modules.length - 4} more
                </li>
              )}
            </ul>
          </div>
        </div>
      </div>

      {daysRemaining <= 7 && (
        <div className="mt-3 p-3" style={{
          background: 'rgba(255, 82, 82, 0.2)',
          borderRadius: '8px',
          border: '1px solid rgba(255, 82, 82, 0.4)',
        }}>
          <div className="d-flex align-items-center justify-content-between">
            <div className="d-flex align-items-center">
              <i className="fas fa-exclamation-triangle me-2" style={{ color: '#FF5252' }}></i>
              <span style={{ color: '#fff', fontSize: '14px' }}>
                Your subscription expires soon!
              </span>
            </div>
            <Link href="/pricing" className="loginBtn" style={{ fontSize: '14px', padding: '8px 16px' }}>
              Renew Now
            </Link>
          </div>
        </div>
      )}
    </div>
  );
}
