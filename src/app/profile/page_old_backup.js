'use client';

import { useEffect, useState } from 'react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';
import axios from 'axios';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function ProfilePage() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [userData, setUserData] = useState(null);

  useEffect(() => {
    const token = Cookies.get('user-token');
    if (token) {
      setIsAuthenticated(true);
      fetchUserData(token);
    } else {
      setIsAuthenticated(false);
      setLoading(false);
    }
  }, []);

  const fetchUserData = async (token) => {
    try {
      const response = await axios.post(
        `${baseUrl}/api/user-data`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      
      if (response.data) {
        setUserData(response.data.data);
      }
    } catch (error) {
      console.error('Error fetching user data:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <Navbar />
      <ToastContainer />
      
      <div className="main-wrapper">
        <section style={{ minHeight: '80vh', paddingTop: '40px', paddingBottom: '60px' }}>
          <div className="container">
            {/* Page Header */}
            <div className="mb-5">
              <h1 className="text-white mb-2" style={{ fontSize: '32px', fontWeight: '700' }}>
                <i className="fas fa-user-circle me-3" style={{ color: '#126E97' }}></i>
                My Profile
              </h1>
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '16px' }}>
                Manage your account settings and personal information
              </p>
            </div>

            {!isAuthenticated ? (
              <div className="text-center" style={{ minHeight: '300px', paddingTop: '100px' }}>
                <h3 className="text-white mb-4">Please Login to View Profile</h3>
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
            ) : loading ? (
              <div className="text-center" style={{ padding: '60px 0' }}>
                <div className="spinner-border text-primary" role="status">
                  <span className="visually-hidden">Loading...</span>
                </div>
              </div>
            ) : (
              <div className="row">
                {/* Profile Card */}
                <div className="col-lg-4 mb-4">
                  <div style={{
                    background: 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)',
                    borderRadius: '15px',
                    padding: '30px',
                    textAlign: 'center',
                  }}>
                    <div style={{
                      width: '100px',
                      height: '100px',
                      margin: '0 auto 20px',
                      background: 'rgba(255, 255, 255, 0.2)',
                      borderRadius: '50%',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '48px',
                      color: '#fff',
                    }}>
                      <i className="fas fa-user"></i>
                    </div>
                    <h3 className="text-white mb-2" style={{ fontSize: '22px', fontWeight: '600' }}>
                      {userData?.firstname || 'User'}
                    </h3>
                    <p style={{ color: 'rgba(255, 255, 255, 0.80)', fontSize: '14px', marginBottom: '20px' }}>
                      {userData?.email || 'email@example.com'}
                    </p>
                    <div style={{
                      background: 'rgba(255, 255, 255, 0.1)',
                      borderRadius: '8px',
                      padding: '12px',
                      marginTop: '20px',
                    }}>
                      <small style={{ color: 'rgba(255, 255, 255, 0.70)' }}>Member Since</small>
                      <p className="text-white mb-0" style={{ fontSize: '14px', fontWeight: '500' }}>
                        {userData?.created_at 
                          ? new Date(userData.created_at).toLocaleDateString('en-GB', {
                              month: 'long',
                              year: 'numeric'
                            })
                          : 'N/A'}
                      </p>
                    </div>
                  </div>
                </div>

                {/* Profile Details */}
                <div className="col-lg-8">
                  <div style={{
                    background: '#282D41',
                    borderRadius: '15px',
                    padding: '30px',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                  }}>
                    <h4 className="text-white mb-4" style={{ fontSize: '20px', fontWeight: '600' }}>
                      <i className="fas fa-info-circle me-2" style={{ color: '#126E97' }}></i>
                      Account Information
                    </h4>

                    <div className="row">
                      <div className="col-md-6 mb-4">
                        <label style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '13px', marginBottom: '8px' }}>
                          Full Name
                        </label>
                        <div style={{
                          background: '#1B1E27',
                          padding: '12px 16px',
                          borderRadius: '8px',
                          color: '#fff',
                        }}>
                          {userData?.firstname || 'N/A'}
                        </div>
                      </div>

                      <div className="col-md-6 mb-4">
                        <label style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '13px', marginBottom: '8px' }}>
                          Email Address
                        </label>
                        <div style={{
                          background: '#1B1E27',
                          padding: '12px 16px',
                          borderRadius: '8px',
                          color: '#fff',
                        }}>
                          {userData?.email || 'N/A'}
                        </div>
                      </div>

                      <div className="col-md-6 mb-4">
                        <label style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '13px', marginBottom: '8px' }}>
                          Mobile Number
                        </label>
                        <div style={{
                          background: '#1B1E27',
                          padding: '12px 16px',
                          borderRadius: '8px',
                          color: '#fff',
                        }}>
                          {userData?.mobile || 'Not provided'}
                        </div>
                      </div>

                      <div className="col-md-6 mb-4">
                        <label style={{ color: 'rgba(255, 255, 255, 0.60)', fontSize: '13px', marginBottom: '8px' }}>
                          Account Status
                        </label>
                        <div style={{
                          background: '#1B1E27',
                          padding: '12px 16px',
                          borderRadius: '8px',
                        }}>
                          <span style={{
                            background: '#4CAF50',
                            color: '#fff',
                            padding: '4px 12px',
                            borderRadius: '12px',
                            fontSize: '12px',
                            fontWeight: '600',
                          }}>
                            Active
                          </span>
                        </div>
                      </div>
                    </div>

                    <div className="mt-4">
                      <button 
                        className="loginBtn me-2"
                        onClick={() => toast.info('Profile editing coming soon!')}
                      >
                        <i className="fas fa-edit me-2"></i>
                        Edit Profile
                      </button>
                      <button 
                        className="loginBtn"
                        style={{ background: '#FF5252' }}
                        onClick={() => toast.info('Password change coming soon!')}
                      >
                        <i className="fas fa-key me-2"></i>
                        Change Password
                      </button>
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
