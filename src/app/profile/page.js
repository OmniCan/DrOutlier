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
  const [countries, setCountries] = useState([]);
  const [saving, setSaving] = useState(false);
  
  // Edit modes
  const [editingProfile, setEditingProfile] = useState(false);
  const [editingPassword, setEditingPassword] = useState(false);
  
  // Form data
  const [profileData, setProfileData] = useState({
    firstname: '',
    lastname: '',
    mobile: '',
    country_code: '+91',
    photo: null,
    photoPreview: null
  });
  
  const [passwordData, setPasswordData] = useState({
    old_password: '',
    new_password: '',
    confirm_password: ''
  });

  useEffect(() => {
    const token = Cookies.get('user-token');
    const userId = Cookies.get('user-id');
    const username = Cookies.get('Login-user');
    
    if (token) {
      setIsAuthenticated(true);
      fetchUserData(token, userId, username);
      fetchCountries();
    } else {
      setIsAuthenticated(false);
      setLoading(false);
    }
  }, []);

  const fetchUserData = async (token, userId, username) => {
    try {
      // Try the API call
      const response = await axios.post(
        `${baseUrl}/api/user-data`,
        { user_id: userId },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      
      console.log('API Response:', response.data);
      
      // Check if we got valid user data from API
      if (response.data && response.data.status === 'success' && response.data.data && response.data.data.list) {
        const user = response.data.data.list;
        console.log('User data from API:', user);
        
        setUserData(user);
        setProfileData({
          firstname: user.firstname || username || '',
          lastname: user.lastname || '',
          mobile: user.mobile || '',
          country_code: user.country_code || '+91',
          photo: null,
          photoPreview: user.image ? `${baseUrl}/${user.image}` : (user.avatar ? user.avatar : null)
        });
      } else {
        console.log('API returned null or invalid data, using fallback');
        // API returned null, use cookie data as fallback
        const userEmail = Cookies.get('user-email') || Cookies.get('email') || '';
        const fallbackUser = {
          firstname: username || 'User',
          lastname: '',
          email: userEmail,
          mobile: '',
          country_code: '+91',
          image: null,
          created_at: null
        };
        setUserData(fallbackUser);
        setProfileData({
          firstname: username || '',
          lastname: '',
          mobile: '',
          country_code: '+91',
          photo: null,
          photoPreview: null
        });
      }
    } catch (error) {
      console.error('Error fetching user data:', error);
      
      // Use cookie fallback on error too
      const username = Cookies.get('Login-user');
      const userEmail = Cookies.get('user-email') || Cookies.get('email') || '';
      const fallbackUser = {
        firstname: username || 'User',
        lastname: '',
        email: userEmail,
        mobile: '',
        country_code: '+91',
        image: null,
        created_at: null
      };
      setUserData(fallbackUser);
      setProfileData({
        firstname: username || '',
        lastname: '',
        mobile: '',
        country_code: '+91',
        photo: null,
        photoPreview: null
      });
    } finally {
      setLoading(false);
    }
  };

  const fetchCountries = async () => {
    try {
      const response = await axios.get(`${baseUrl}/api/get-countries`);
      if (response.data.success) {
        setCountries(response.data.data);
      }
    } catch (error) {
      console.error('Error fetching countries:', error);
    }
  };

  const handleProfileChange = (e) => {
    const { name, value } = e.target;
    setProfileData(prev => ({ ...prev, [name]: value }));
  };

  const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      if (file.size > 5 * 1024 * 1024) {
        toast.error('Photo size should be less than 5MB');
        return;
      }
      setProfileData(prev => ({
        ...prev,
        photo: file,
        photoPreview: URL.createObjectURL(file)
      }));
    }
  };

  const handlePasswordChange = (e) => {
    const { name, value } = e.target;
    setPasswordData(prev => ({ ...prev, [name]: value }));
  };

  const handleProfileSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);

    try {
      const userId = Cookies.get('user-id');
      const token = Cookies.get('user-token');

      // Create a clean payload with only allowed fields
      const payload = {
        user_id: userId,
        firstname: profileData.firstname.trim(),
        lastname: (profileData.lastname || '').trim(),
        mobile: (profileData.mobile || '').trim(),
        country_code: profileData.country_code
      };

      // If there's a photo, we need to use FormData
      if (profileData.photo) {
        const formData = new FormData();
        // Only append the exact fields we need
        Object.keys(payload).forEach(key => {
          formData.append(key, payload[key]);
        });
        formData.append('image', profileData.photo);

        const response = await axios.post(`${baseUrl}/api/profile-update`, formData, {
          headers: {
            'Authorization': `Bearer ${token}`
            // Don't set Content-Type for FormData, let axios handle it
          }
        });

        if (response.data.success) {
          toast.success('Profile updated successfully!');
          
          // Update userData state immediately with the new values
          setUserData(prev => ({
            ...prev,
            firstname: payload.firstname,
            lastname: payload.lastname,
            mobile: payload.mobile,
            country_code: payload.country_code
          }));
          
          // Update Login-user cookie if firstname changed
          if (payload.firstname) {
            Cookies.set('Login-user', payload.firstname);
          }
          
          setEditingProfile(false);
        } else {
          toast.error(response.data.message || 'Failed to update profile');
        }
      } else {
        // Send as JSON when no photo
        const response = await axios.post(`${baseUrl}/api/profile-update`, payload, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });

        if (response.data.success) {
          toast.success('Profile updated successfully!');
          
          // Update userData state immediately with the new values
          setUserData(prev => ({
            ...prev,
            firstname: payload.firstname,
            lastname: payload.lastname,
            mobile: payload.mobile,
            country_code: payload.country_code
          }));
          
          // Update Login-user cookie if firstname changed
          if (payload.firstname) {
            Cookies.set('Login-user', payload.firstname);
          }
          
          setEditingProfile(false);
        } else {
          toast.error(response.data.message || 'Failed to update profile');
        }
      }
    } catch (error) {
      console.error('Profile update error:', error.response?.data || error.message);
      const errorMsg = error.response?.data?.message || error.response?.data?.error || 'An error occurred while updating profile';
      toast.error(errorMsg);
    } finally {
      setSaving(false);
    }
  };

  const handlePasswordSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);

    if (passwordData.new_password !== passwordData.confirm_password) {
      toast.error('New passwords do not match');
      setSaving(false);
      return;
    }

    if (passwordData.new_password.length < 6) {
      toast.error('Password must be at least 6 characters');
      setSaving(false);
      return;
    }

    try {
      const userId = Cookies.get('user-id');
      const token = Cookies.get('user-token');
      const response = await axios.post(`${baseUrl}/api/change-password`, {
        user_id: userId,
        old_password: passwordData.old_password,
        new_password: passwordData.new_password
      }, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      if (response.data.success) {
        toast.success('Password changed successfully!');
        setEditingPassword(false);
        setPasswordData({ old_password: '', new_password: '', confirm_password: '' });
      } else {
        toast.error(response.data.message || 'Failed to change password');
      }
    } catch (error) {
      toast.error('An error occurred while changing password');
    } finally {
      setSaving(false);
    }
  };

  return (
    <>
      <Navbar />
      <ToastContainer position="top-right" theme="dark" />
      
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
                {/* Profile Card with Photo */}
                <div className="col-lg-4 mb-4">
                  <div style={{
                    background: 'linear-gradient(135deg, #126E97 0%, #0d5070 100%)',
                    borderRadius: '15px',
                    padding: '30px',
                    textAlign: 'center',
                  }}>
                    <div style={{ position: 'relative', display: 'inline-block', marginBottom: '20px' }}>
                      {profileData.photoPreview ? (
                        <img 
                          src={profileData.photoPreview}
                          alt="Profile"
                          style={{
                            width: '120px',
                            height: '120px',
                            borderRadius: '50%',
                            objectFit: 'cover',
                            border: '4px solid rgba(255, 255, 255, 0.3)'
                          }}
                        />
                      ) : (
                        <div style={{
                          width: '120px',
                          height: '120px',
                          background: 'rgba(255, 255, 255, 0.2)',
                          borderRadius: '50%',
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'center',
                          fontSize: '48px',
                          color: '#fff',
                          border: '4px solid rgba(255, 255, 255, 0.3)'
                        }}>
                          <i className="fas fa-user"></i>
                        </div>
                      )}
                      
                      {editingProfile && (
                        <>
                          <label 
                            htmlFor="photoUpload" 
                            style={{
                              position: 'absolute',
                              bottom: '0',
                              right: '0',
                              background: '#fff',
                              width: '36px',
                              height: '36px',
                              borderRadius: '50%',
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              cursor: 'pointer',
                              border: '3px solid #126E97',
                              color: '#126E97'
                            }}
                          >
                            <i className="fas fa-camera"></i>
                          </label>
                          <input 
                            type="file" 
                            id="photoUpload"
                            accept="image/*"
                            onChange={handlePhotoChange}
                            style={{ display: 'none' }}
                          />
                        </>
                      )}
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

                {/* Profile Details Form */}
                <div className="col-lg-8">
                  <div style={{
                    background: '#282D41',
                    borderRadius: '15px',
                    padding: '30px',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                    marginBottom: '20px'
                  }}>
                    <div className="d-flex justify-content-between align-items-center mb-4">
                      <h4 className="text-white mb-0" style={{ fontSize: '20px', fontWeight: '600' }}>
                        <i className="fas fa-info-circle me-2" style={{ color: '#126E97' }}></i>
                        Account Information
                      </h4>
                      {!editingProfile && (
                        <button 
                          className="loginBtn"
                          onClick={() => setEditingProfile(true)}
                          style={{ padding: '8px 20px', fontSize: '14px' }}
                        >
                          <i className="fas fa-edit me-2"></i>
                          Edit Profile
                        </button>
                      )}
                    </div>

                    {editingProfile ? (
                      <form onSubmit={handleProfileSubmit}>
                        <div className="row">
                          <div className="col-md-6 mb-3">
                            <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                              <i className="fas fa-user me-2"></i>First Name
                            </label>
                            <input 
                              type="text"
                              name="firstname"
                              className="form-control"
                              value={profileData.firstname}
                              onChange={handleProfileChange}
                              required
                              style={{
                                background: '#1B1E27',
                                border: '1px solid rgba(255, 255, 255, 0.1)',
                                color: '#fff',
                                padding: '12px 16px',
                                borderRadius: '8px'
                              }}
                            />
                          </div>

                          <div className="col-md-6 mb-3">
                            <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                              <i className="fas fa-user me-2"></i>Last Name
                            </label>
                            <input 
                              type="text"
                              name="lastname"
                              className="form-control"
                              value={profileData.lastname}
                              onChange={handleProfileChange}
                              style={{
                                background: '#1B1E27',
                                border: '1px solid rgba(255, 255, 255, 0.1)',
                                color: '#fff',
                                padding: '12px 16px',
                                borderRadius: '8px'
                              }}
                            />
                          </div>

                          <div className="col-12 mb-3">
                            <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                              <i className="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input 
                              type="email"
                              value={userData?.email || ''}
                              disabled
                              style={{
                                background: '#1B1E27',
                                border: '1px solid rgba(255, 255, 255, 0.1)',
                                color: '#999',
                                padding: '12px 16px',
                                borderRadius: '8px',
                                width: '100%'
                              }}
                            />
                            <small style={{ color: 'rgba(255, 255, 255, 0.50)', fontSize: '12px' }}>
                              Email cannot be changed
                            </small>
                          </div>

                          <div className="col-12 mb-3">
                            <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                              <i className="fas fa-phone me-2"></i>Mobile Number
                            </label>
                            <div className="row g-2">
                              <div className="col-md-4">
                                <select
                                  name="country_code"
                                  className="form-select"
                                  value={profileData.country_code}
                                  onChange={handleProfileChange}
                                  style={{
                                    background: '#1B1E27',
                                    border: '1px solid rgba(255, 255, 255, 0.1)',
                                    color: '#fff',
                                    padding: '12px 16px',
                                    borderRadius: '8px'
                                  }}
                                >
                                  {countries.map(country => (
                                    <option 
                                      key={country.id} 
                                      value={country.dial_code}
                                      style={{ background: '#1B1E27', color: '#fff' }}
                                    >
                                      {country.dial_code} - {country.name}
                                    </option>
                                  ))}
                                </select>
                              </div>
                              <div className="col-md-8">
                                <input 
                                  type="tel"
                                  name="mobile"
                                  className="form-control"
                                  value={profileData.mobile}
                                  onChange={handleProfileChange}
                                  placeholder="Enter mobile number"
                                  style={{
                                    background: '#1B1E27',
                                    border: '1px solid rgba(255, 255, 255, 0.1)',
                                    color: '#fff',
                                    padding: '12px 16px',
                                    borderRadius: '8px'
                                  }}
                                />
                              </div>
                            </div>
                            <small style={{ color: 'rgba(255, 255, 255, 0.50)', fontSize: '12px' }}>
                              Enter 10-digit mobile number
                            </small>
                          </div>
                        </div>

                        <div className="mt-4 d-flex gap-2">
                          <button 
                            type="submit" 
                            className="loginBtn"
                            disabled={saving}
                          >
                            {saving ? (
                              <>
                                <span className="spinner-border spinner-border-sm me-2"></span>
                                Saving...
                              </>
                            ) : (
                              <>
                                <i className="fas fa-save me-2"></i>
                                Save Changes
                              </>
                            )}
                          </button>
                          <button 
                            type="button"
                            className="loginBtn"
                            onClick={() => {
                              setEditingProfile(false);
                              const username = Cookies.get('Login-user');
                              setProfileData({
                                firstname: userData?.firstname || username || '',
                                lastname: userData?.lastname || '',
                                mobile: userData?.mobile || '',
                                country_code: userData?.country_code || '+91',
                                photo: null,
                                photoPreview: userData?.image ? `${baseUrl}/${userData.image}` : (userData?.avatar ? userData.avatar : null)
                              });
                            }}
                            style={{ background: '#6c757d' }}
                          >
                            Cancel
                          </button>
                        </div>
                      </form>
                    ) : (
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
                            {userData?.firstname ? `${userData.firstname}${userData.lastname ? ' ' + userData.lastname : ''}` : 'Not provided'}
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
                            {userData?.mobile ? `${userData.country_code || '+91'} ${userData.mobile}` : 'Not provided'}
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
                    )}
                  </div>

                  {/* Change Password Section */}
                  <div style={{
                    background: '#282D41',
                    borderRadius: '15px',
                    padding: '30px',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                  }}>
                    <div className="d-flex justify-content-between align-items-center mb-4">
                      <h4 className="text-white mb-0" style={{ fontSize: '20px', fontWeight: '600' }}>
                        <i className="fas fa-lock me-2" style={{ color: '#126E97' }}></i>
                        Password & Security
                      </h4>
                      {!editingPassword && (
                        <button 
                          className="loginBtn"
                          onClick={() => setEditingPassword(true)}
                          style={{ padding: '8px 20px', fontSize: '14px', background: '#FF5252' }}
                        >
                          <i className="fas fa-key me-2"></i>
                          Change Password
                        </button>
                      )}
                    </div>

                    {editingPassword ? (
                      <form onSubmit={handlePasswordSubmit}>
                        <div className="mb-3">
                          <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                            <i className="fas fa-key me-2"></i>Current Password
                          </label>
                          <input 
                            type="password"
                            name="old_password"
                            className="form-control"
                            value={passwordData.old_password}
                            onChange={handlePasswordChange}
                            required
                            style={{
                              background: '#1B1E27',
                              border: '1px solid rgba(255, 255, 255, 0.1)',
                              color: '#fff',
                              padding: '12px 16px',
                              borderRadius: '8px'
                            }}
                          />
                        </div>

                        <div className="mb-3">
                          <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                            <i className="fas fa-key me-2"></i>New Password
                          </label>
                          <input 
                            type="password"
                            name="new_password"
                            className="form-control"
                            value={passwordData.new_password}
                            onChange={handlePasswordChange}
                            required
                            minLength="6"
                            style={{
                              background: '#1B1E27',
                              border: '1px solid rgba(255, 255, 255, 0.1)',
                              color: '#fff',
                              padding: '12px 16px',
                              borderRadius: '8px'
                            }}
                          />
                          <small style={{ color: 'rgba(255, 255, 255, 0.50)', fontSize: '12px' }}>
                            Minimum 6 characters
                          </small>
                        </div>

                        <div className="mb-4">
                          <label style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '13px', marginBottom: '8px' }}>
                            <i className="fas fa-key me-2"></i>Confirm New Password
                          </label>
                          <input 
                            type="password"
                            name="confirm_password"
                            className="form-control"
                            value={passwordData.confirm_password}
                            onChange={handlePasswordChange}
                            required
                            minLength="6"
                            style={{
                              background: '#1B1E27',
                              border: '1px solid rgba(255, 255, 255, 0.1)',
                              color: '#fff',
                              padding: '12px 16px',
                              borderRadius: '8px'
                            }}
                          />
                        </div>

                        <div className="d-flex gap-2">
                          <button 
                            type="submit" 
                            className="loginBtn"
                            disabled={saving}
                            style={{ background: '#FF5252' }}
                          >
                            {saving ? (
                              <>
                                <span className="spinner-border spinner-border-sm me-2"></span>
                                Changing...
                              </>
                            ) : (
                              <>
                                <i className="fas fa-shield-alt me-2"></i>
                                Change Password
                              </>
                            )}
                          </button>
                          <button 
                            type="button"
                            className="loginBtn"
                            onClick={() => {
                              setEditingPassword(false);
                              setPasswordData({ old_password: '', new_password: '', confirm_password: '' });
                            }}
                            style={{ background: '#6c757d' }}
                          >
                            Cancel
                          </button>
                        </div>
                      </form>
                    ) : (
                      <p style={{ color: 'rgba(255, 255, 255, 0.60)', marginBottom: 0 }}>
                        Click "Change Password" to update your password. Make sure to use a strong, unique password.
                      </p>
                    )}
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
