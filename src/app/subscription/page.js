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
        <section style={{ minHeight: '80vh', paddingTop: '40px', paddingBottom: '60px' }}>
          <div className="container">
            {/* Page Header with Actions */}
            <div className="row align-items-center mb-5">
              <div className="col-md-8">
                <h1 className="text-white mb-2" style={{ fontSize: '32px', fontWeight: '700' }}>
                  <i className="fas fa-crown me-3" style={{ color: '#FFA500' }}></i>
                  My Subscription
                </h1>
                <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '16px', marginBottom: 0 }}>
                  Manage your subscription, track your access, and view purchase history
                </p>
              </div>
              <div className="col-md-4 text-md-end mt-3 mt-md-0">
                <a 
                  href="/pricing" 
                  className="loginBtn"
                  style={{ 
                    display: 'inline-flex', 
                    alignItems: 'center', 
                    gap: '8px',
                    padding: '12px 24px',
                    fontSize: '15px'
                  }}
                >
                  <i className="fas fa-arrow-up"></i>
                  Upgrade Plan
                </a>
              </div>
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

                {/* Quick Stats */}
                <div className="row mb-5">
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)',
                      borderRadius: '10px',
                      padding: '20px',
                      textAlign: 'center',
                    }}>
                      <i className="fas fa-calendar-check mb-2" style={{ fontSize: '28px', color: '#FFA500' }}></i>
                      <h3 className="text-white mb-1" style={{ fontSize: '24px', fontWeight: '700' }}>
                        {history.filter(s => s.status === 'active').length}
                      </h3>
                      <p style={{ color: 'rgba(255, 255, 255, 0.80)', fontSize: '14px', margin: 0 }}>
                        Active Subscriptions
                      </p>
                    </div>
                  </div>
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: '#282D41',
                      borderRadius: '10px',
                      padding: '20px',
                      textAlign: 'center',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                    }}>
                      <i className="fas fa-history mb-2" style={{ fontSize: '28px', color: '#4CAF50' }}></i>
                      <h3 className="text-white mb-1" style={{ fontSize: '24px', fontWeight: '700' }}>
                        {history.length}
                      </h3>
                      <p style={{ color: 'rgba(255, 255, 255, 0.80)', fontSize: '14px', margin: 0 }}>
                        Total Purchases
                      </p>
                    </div>
                  </div>
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: '#282D41',
                      borderRadius: '10px',
                      padding: '20px',
                      textAlign: 'center',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                    }}>
                      <i className="fas fa-rupee-sign mb-2" style={{ fontSize: '28px', color: '#FFA500' }}></i>
                      <h3 className="text-white mb-1" style={{ fontSize: '24px', fontWeight: '700' }}>
                        ₹{history.reduce((sum, s) => sum + (s.amount_paid || 0), 0)}
                      </h3>
                      <p style={{ color: 'rgba(255, 255, 255, 0.80)', fontSize: '14px', margin: 0 }}>
                        Total Spent
                      </p>
                    </div>
                  </div>
                </div>

                {/* Subscription History */}
                <div>
                  <div className="d-flex justify-content-between align-items-center mb-4">
                    <h4 className="text-white mb-0" style={{ fontSize: '22px', fontWeight: '600' }}>
                      <i className="fas fa-receipt me-2" style={{ color: '#126E97' }}></i>
                      Subscription History
                    </h4>
                    {history.length > 0 && (
                      <span style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '14px' }}>
                        {history.length} {history.length === 1 ? 'transaction' : 'transactions'}
                      </span>
                    )}
                  </div>
                  
                  {history.length === 0 ? (
                    <div style={{
                      background: 'linear-gradient(135deg, #282D41 0%, #1B1E27 100%)',
                      borderRadius: '15px',
                      padding: '60px 40px',
                      textAlign: 'center',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                    }}>
                      <div style={{
                        width: '80px',
                        height: '80px',
                        margin: '0 auto 20px',
                        background: 'rgba(18, 110, 151, 0.2)',
                        borderRadius: '50%',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                      }}>
                        <i className="fas fa-receipt" style={{ fontSize: '36px', color: '#126E97' }}></i>
                      </div>
                      <h5 className="text-white mb-3" style={{ fontSize: '20px', fontWeight: '600' }}>
                        No Purchase History Yet
                      </h5>
                      <p style={{ color: 'rgba(255, 255, 255, 0.60)', marginBottom: '24px', fontSize: '15px' }}>
                        Start your learning journey by subscribing to one of our plans
                      </p>
                      <a href="/pricing" className="loginBtn">
                        <i className="fas fa-tags me-2"></i>
                        Browse Plans
                      </a>
                    </div>
                  ) : (
                    <div className="table-responsive" style={{
                      borderRadius: '12px',
                      border: '1px solid rgba(255, 255, 255, 0.1)',
                      overflow: 'hidden',
                    }}>
                      <table className="table mb-0" style={{
                        background: '#282D41',
                      }}>
                        <thead style={{ background: '#1B1E27' }}>
                          <tr>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Plan
                            </th>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Amount
                            </th>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Status
                            </th>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Started
                            </th>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Expires
                            </th>
                            <th className="text-white" style={{ 
                              padding: '18px 20px', 
                              borderBottom: '2px solid rgba(255, 255, 255, 0.1)',
                              fontSize: '13px',
                              fontWeight: '600',
                              textTransform: 'uppercase',
                              letterSpacing: '0.5px'
                            }}>
                              Purchased
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          {history.map((subscription, index) => (
                            <tr 
                              key={subscription.id} 
                              style={{
                                borderBottom: index < history.length - 1 ? '1px solid rgba(255, 255, 255, 0.05)' : 'none',
                                transition: 'background 0.2s ease',
                              }}
                              onMouseEnter={(e) => {
                                const cells = e.currentTarget.querySelectorAll('td');
                                cells.forEach(cell => cell.style.background = 'rgba(18, 110, 151, 0.15)');
                              }}
                              onMouseLeave={(e) => {
                                const cells = e.currentTarget.querySelectorAll('td');
                                cells.forEach(cell => cell.style.background = '#282D41');
                              }}
                            >
                              <td style={{ padding: '18px 20px', background: '#282D41' }}>
                                <div className="d-flex align-items-center">
                                  <div style={{
                                    width: '36px',
                                    height: '36px',
                                    background: 'rgba(18, 110, 151, 0.3)',
                                    borderRadius: '8px',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    marginRight: '12px',
                                  }}>
                                    <i className="fas fa-box" style={{ color: '#4FC3F7', fontSize: '16px' }}></i>
                                  </div>
                                  <div>
                                    <div style={{ color: '#FFFFFF', fontWeight: '600', fontSize: '15px' }}>
                                      {subscription.plan_name}
                                    </div>
                                  </div>
                                </div>
                              </td>
                              <td style={{ padding: '18px 20px', background: '#282D41' }}>
                                <span style={{ 
                                  color: '#FFA500', 
                                  fontWeight: '700',
                                  fontSize: '17px',
                                }}>
                                  ₹{subscription.amount_paid}
                                </span>
                              </td>
                              <td style={{ padding: '18px 20px', background: '#282D41' }}>
                                {getStatusBadge(subscription.status)}
                              </td>
                              <td style={{ padding: '18px 20px', background: '#282D41', color: '#FFFFFF', fontSize: '14px', fontWeight: '500' }}>
                                {subscription.started_at 
                                  ? new Date(subscription.started_at).toLocaleDateString('en-GB', {
                                      day: '2-digit',
                                      month: 'short',
                                      year: 'numeric'
                                    })
                                  : '-'}
                              </td>
                              <td style={{ padding: '18px 20px', background: '#282D41', color: '#FFFFFF', fontSize: '14px', fontWeight: '500' }}>
                                {subscription.expires_at 
                                  ? new Date(subscription.expires_at).toLocaleDateString('en-GB', {
                                      day: '2-digit',
                                      month: 'short',
                                      year: 'numeric'
                                    })
                                  : '-'}
                              </td>
                              <td style={{ padding: '18px 20px', background: '#282D41', color: 'rgba(255, 255, 255, 0.80)', fontSize: '14px' }}>
                                <i className="far fa-calendar-alt me-2" style={{ color: '#4FC3F7' }}></i>
                                {new Date(subscription.created_at).toLocaleDateString('en-GB', {
                                  day: '2-digit',
                                  month: 'short',
                                  year: 'numeric'
                                })}
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
