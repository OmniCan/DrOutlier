'use client';

import { useEffect, useState } from 'react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import SubscriptionStatus from '@/components/SubscriptionStatus';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';
import { useRouter } from 'next/navigation';
import Loader from '@/components/Loader';

/**
 * User Subscription Dashboard Page
 * Shows current subscription and history
 */
export default function SubscriptionPage() {
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const router = useRouter();

  useEffect(() => {
    const token = Cookies.get('user-token');
    if (token) {
      setIsAuthenticated(true);
      fetchHistory(token);
    } else {
      setIsAuthenticated(false);
      setLoading(false);
    }
  }, []);

  const fetchHistory = async (token) => {
    try {
      const response = await fetch(`${baseUrl}/api/subscription/history`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      const data = await response.json();

      if (data.status === 'success') {
        setHistory(data.data.subscriptions);
      }
    } catch (error) {
      console.error('Error fetching subscription history:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status) => {
    const badges = {
      active: { color: '#4CAF50', text: 'Active' },
      expired: { color: '#FF5252', text: 'Expired' },
      pending: { color: '#FFA500', text: 'Pending' },
      cancelled: { color: '#9E9E9E', text: 'Cancelled' },
    };

    const badge = badges[status] || badges.pending;

    return (
      <span style={{
        background: badge.color,
        color: '#fff',
        padding: '4px 12px',
        borderRadius: '12px',
        fontSize: '12px',
        fontWeight: '600',
      }}>
        {badge.text}
      </span>
    );
  };

  return (
    <>
      <Navbar />
      
      <div className="main-wrapper">
        <section style={{ minHeight: '80vh', paddingTop: '40px', paddingBottom: '40px' }}>
          <div className="container">
            <div className="mb-4">
              <h2 className="text-white mb-2">My Subscription</h2>
              <p style={{ color: 'rgba(255, 255, 255, 0.60)' }}>
                Manage your subscription and view your purchase history
              </p>
            </div>

            {loading ? (
              <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '300px' }}>
                <Loader />
              </div>
            ) : !isAuthenticated ? (
              <div className="text-center" style={{ minHeight: '300px', paddingTop: '100px' }}>
                <h3 className="text-white mb-4">Please Login to View Subscription</h3>
                <button 
                  onClick={() => {
                    const myModal = new bootstrap.Modal(document.getElementById('myModal'));
                    myModal.show();
                  }} 
                  className="loginBtn"
                >
                  Login Now
                </button>
              </div>
            ) : (
              <>
                {/* Current Subscription Status */}
                <div className="mb-5">
                  <SubscriptionStatus />
                </div>

                {/* Subscription History */}
                <div>
                  <h4 className="text-white mb-4">Subscription History</h4>
                  
                  {history.length === 0 ? (
                    <div style={{
                      background: '#282D41',
                      borderRadius: '10px',
                      padding: '40px',
                      textAlign: 'center',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                    }}>
                      <i className="fas fa-history mb-3" style={{ fontSize: '48px', color: 'rgba(255, 255, 255, 0.3)' }}></i>
                      <h5 className="text-white mb-2">No Subscription History</h5>
                      <p style={{ color: 'rgba(255, 255, 255, 0.60)' }}>
                        You haven't purchased any subscriptions yet
                      </p>
                    </div>
                  ) : (
                    <div className="table-responsive">
                      <table className="table" style={{
                        background: '#282D41',
                        borderRadius: '10px',
                        overflow: 'hidden',
                      }}>
                        <thead style={{ background: '#1B1E27' }}>
                          <tr>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Plan
                            </th>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Amount
                            </th>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Status
                            </th>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Started
                            </th>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Expires
                            </th>
                            <th className="text-white" style={{ padding: '15px', borderBottom: '2px solid rgba(255, 255, 255, 0.1)' }}>
                              Purchased
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          {history.map((subscription, index) => (
                            <tr key={subscription.id} style={{
                              borderBottom: index < history.length - 1 ? '1px solid rgba(255, 255, 255, 0.05)' : 'none',
                            }}>
                              <td style={{ padding: '15px', color: 'rgba(255, 255, 255, 0.80)' }}>
                                <div className="d-flex align-items-center">
                                  <i className="fas fa-box me-2" style={{ color: '#126E97' }}></i>
                                  {subscription.plan_name}
                                </div>
                              </td>
                              <td style={{ padding: '15px', color: 'rgba(255, 255, 255, 0.80)', fontWeight: '600' }}>
                                ₹{subscription.amount_paid}
                              </td>
                              <td style={{ padding: '15px' }}>
                                {getStatusBadge(subscription.status)}
                              </td>
                              <td style={{ padding: '15px', color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                                {subscription.started_at 
                                  ? new Date(subscription.started_at).toLocaleDateString('en-GB')
                                  : '-'}
                              </td>
                              <td style={{ padding: '15px', color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                                {subscription.expires_at 
                                  ? new Date(subscription.expires_at).toLocaleDateString('en-GB')
                                  : '-'}
                              </td>
                              <td style={{ padding: '15px', color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                                {new Date(subscription.created_at).toLocaleDateString('en-GB')}
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  )}
                </div>
              </>
            )}
          </div>
        </section>
      </div>

      <Footer />
    </>
  );
}
