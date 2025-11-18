'use client';

import { useEffect, useState } from 'react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';
import axios from 'axios';
import Link from 'next/link';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function BookmarksPage() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [bookmarks, setBookmarks] = useState({
    notes: [],
    spotters: [],
    osce: [],
    quizora: [],
    aiRad: [],
    practicalEssentials: [],
    watchAndLearn: []
  });
  const [activeAccordion, setActiveAccordion] = useState('notes');

  useEffect(() => {
    const token = Cookies.get('user-token');
    if (token) {
      setIsAuthenticated(true);
      fetchAllBookmarks();
    } else {
      setIsAuthenticated(false);
      setLoading(false);
    }
  }, []);

  const fetchAllBookmarks = async () => {
    try {
      const userId = Cookies.get('user-id');
      const token = Cookies.get('user-token');
      
      const config = {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      };
      
      // Fetch all bookmark types in parallel
      const [notesRes, spottersRes, osceRes, quizRes] = await Promise.all([
        axios.post(`${baseUrl}/api/note/get-note-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/spotters/get-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/osce/get-osce-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/quiz/bookmarks`, { user_id: userId }, config)
      ]);

      setBookmarks({
        notes: notesRes.data.data || [],
        spotters: spottersRes.data.data || [],
        osce: osceRes.data.data || [],
        quizora: quizRes.data.data || [],
        aiRad: [], // Add when API available
        practicalEssentials: [],
        watchAndLearn: []
      });
    } catch (error) {
      console.error('Error fetching bookmarks:', error);
      toast.error('Failed to load bookmarks');
    } finally {
      setLoading(false);
    }
  };

  const removeBookmark = async (type, id) => {
    try {
      const userId = Cookies.get('user-id');
      const token = Cookies.get('user-token');
      let endpoint = '';
      
      const config = {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      };
      
      switch(type) {
        case 'notes':
          endpoint = '/api/note/change-note-bookmark-status';
          break;
        case 'spotters':
          endpoint = '/api/spotters/change-bookmark-status';
          break;
        case 'osce':
          endpoint = '/api/osce/change-osce-bookmark-status';
          break;
        case 'quizora':
          endpoint = '/api/quiz/toggle-bookmark';
          break;
      }

      await axios.post(`${baseUrl}${endpoint}`, {
        user_id: userId,
        [type === 'notes' ? 'note_id' : type === 'osce' ? 'osce_id' : 'id']: id
      }, config);

      // Refresh bookmarks
      fetchAllBookmarks();
      toast.success('Bookmark removed successfully');
    } catch (error) {
      toast.error('Failed to remove bookmark');
    }
  };

  const categories = [
    { 
      id: 'notes', 
      label: 'Notes', 
      icon: 'fas fa-file-alt', 
      color: '#4CAF50',
      count: bookmarks.notes.length
    },
    { 
      id: 'spotters', 
      label: 'Spotters', 
      icon: 'fas fa-image', 
      color: '#2196F3',
      count: bookmarks.spotters.length
    },
    { 
      id: 'osce', 
      label: 'OSCE', 
      icon: 'fas fa-stethoscope', 
      color: '#FF9800',
      count: bookmarks.osce.length
    },
    { 
      id: 'quizora', 
      label: 'Quizora', 
      icon: 'fas fa-question-circle', 
      color: '#9C27B0',
      count: bookmarks.quizora.length
    },
    { 
      id: 'aiRad', 
      label: 'AI-Rad', 
      icon: 'fas fa-brain', 
      color: '#00BCD4',
      count: bookmarks.aiRad.length
    },
    { 
      id: 'practicalEssentials', 
      label: 'Practical Essentials', 
      icon: 'fas fa-flask', 
      color: '#E91E63',
      count: bookmarks.practicalEssentials.length
    },
    { 
      id: 'watchAndLearn', 
      label: 'Watch & Learn', 
      icon: 'fas fa-play-circle', 
      color: '#FF5722',
      count: bookmarks.watchAndLearn.length
    }
  ];

  const renderBookmarkCard = (item, type) => {
    return (
      <div 
        key={item.id} 
        style={{
          background: '#1B1E27',
          borderRadius: '12px',
          padding: '20px',
          marginBottom: '16px',
          border: '1px solid rgba(255, 255, 255, 0.1)',
          transition: 'all 0.3s ease'
        }}
        onMouseEnter={(e) => {
          e.currentTarget.style.borderColor = categories.find(c => c.id === type)?.color;
          e.currentTarget.style.transform = 'translateY(-2px)';
        }}
        onMouseLeave={(e) => {
          e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.1)';
          e.currentTarget.style.transform = 'translateY(0)';
        }}
      >
        <div className="d-flex justify-content-between align-items-start">
          <div style={{ flex: 1 }}>
            {/* Title */}
            <h5 className="text-white mb-2" style={{ fontSize: '18px', fontWeight: '600' }}>
              <i className={`${categories.find(c => c.id === type)?.icon} me-2`} style={{ color: categories.find(c => c.id === type)?.color }}></i>
              {item.title || item.name || item.question || 'Untitled'}
            </h5>
            
            {/* Description/Content */}
            {item.description && (
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px', marginBottom: '12px' }}>
                {item.description.length > 150 ? `${item.description.substring(0, 150)}...` : item.description}
              </p>
            )}
            
            {/* Metadata */}
            <div className="d-flex flex-wrap gap-3">
              {item.category && (
                <span style={{
                  background: 'rgba(255, 255, 255, 0.1)',
                  padding: '4px 12px',
                  borderRadius: '6px',
                  fontSize: '12px',
                  color: 'rgba(255, 255, 255, 0.80)'
                }}>
                  <i className="fas fa-folder me-1"></i>
                  {item.category}
                </span>
              )}
              {item.created_at && (
                <span style={{
                  fontSize: '12px',
                  color: 'rgba(255, 255, 255, 0.50)'
                }}>
                  <i className="fas fa-clock me-1"></i>
                  {new Date(item.created_at).toLocaleDateString()}
                </span>
              )}
            </div>
          </div>

          {/* Actions */}
          <div className="d-flex gap-2 ms-3">
            <Link 
              href={`/${type}/${item.id}`}
              className="btn btn-sm"
              style={{
                background: categories.find(c => c.id === type)?.color,
                color: '#fff',
                border: 'none',
                padding: '8px 16px',
                borderRadius: '6px',
                fontSize: '13px'
              }}
            >
              <i className="fas fa-eye me-1"></i>
              View
            </Link>
            <button
              onClick={() => removeBookmark(type, item.id)}
              className="btn btn-sm"
              style={{
                background: 'rgba(255, 82, 82, 0.2)',
                color: '#FF5252',
                border: '1px solid #FF5252',
                padding: '8px 16px',
                borderRadius: '6px',
                fontSize: '13px'
              }}
            >
              <i className="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    );
  };

  const renderCategoryContent = (category) => {
    const items = bookmarks[category.id];
    
    if (items.length === 0) {
      return (
        <div style={{
          textAlign: 'center',
          padding: '60px 20px',
          background: '#1B1E27',
          borderRadius: '12px',
          border: '1px dashed rgba(255, 255, 255, 0.2)'
        }}>
          <div style={{
            width: '60px',
            height: '60px',
            margin: '0 auto 16px',
            background: `${category.color}33`,
            borderRadius: '50%',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          }}>
            <i className={category.icon} style={{ fontSize: '24px', color: category.color }}></i>
          </div>
          <h5 className="text-white mb-2">No Saved {category.label} Yet</h5>
          <p style={{ color: 'rgba(255, 255, 255, 0.50)', fontSize: '14px' }}>
            Start bookmarking {category.label.toLowerCase()} to see them here
          </p>
        </div>
      );
    }

    return items.map(item => renderBookmarkCard(item, category.id));
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
                <i className="fas fa-bookmark me-3" style={{ color: '#FFA500' }}></i>
                Saved & Bookmarks
              </h1>
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '16px' }}>
                Access all your saved content in one place
              </p>
            </div>

            {!isAuthenticated ? (
              <div className="text-center" style={{ minHeight: '300px', paddingTop: '100px' }}>
                <h3 className="text-white mb-4">Please Login to View Bookmarks</h3>
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
                <p className="text-white mt-3">Loading your bookmarks...</p>
              </div>
            ) : (
              <>
                {/* Summary Cards */}
                <div className="row mb-4">
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)',
                      borderRadius: '12px',
                      padding: '20px',
                      textAlign: 'center'
                    }}>
                      <h2 className="text-white mb-1" style={{ fontSize: '36px', fontWeight: '700' }}>
                        {Object.values(bookmarks).reduce((sum, arr) => sum + arr.length, 0)}
                      </h2>
                      <p className="text-white mb-0" style={{ fontSize: '14px', opacity: 0.9 }}>
                        Total Bookmarks
                      </p>
                    </div>
                  </div>
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: 'linear-gradient(135deg, #2196F3 0%, #1976d2 100%)',
                      borderRadius: '12px',
                      padding: '20px',
                      textAlign: 'center'
                    }}>
                      <h2 className="text-white mb-1" style={{ fontSize: '36px', fontWeight: '700' }}>
                        {Object.values(bookmarks).filter(arr => arr.length > 0).length}
                      </h2>
                      <p className="text-white mb-0" style={{ fontSize: '14px', opacity: 0.9 }}>
                        Active Categories
                      </p>
                    </div>
                  </div>
                  <div className="col-md-4 mb-3">
                    <div style={{
                      background: 'linear-gradient(135deg, #FF9800 0%, #f57c00 100%)',
                      borderRadius: '12px',
                      padding: '20px',
                      textAlign: 'center'
                    }}>
                      <h2 className="text-white mb-1" style={{ fontSize: '36px', fontWeight: '700' }}>
                        {Math.max(...Object.values(bookmarks).map(arr => arr.length))}
                      </h2>
                      <p className="text-white mb-0" style={{ fontSize: '14px', opacity: 0.9 }}>
                        Most Saved
                      </p>
                    </div>
                  </div>
                </div>

                {/* Accordion */}
                <div className="accordion" id="bookmarksAccordion">
                  {categories.map((category, index) => (
                    <div 
                      key={category.id}
                      className="mb-3"
                      style={{
                        background: '#282D41',
                        borderRadius: '12px',
                        border: '1px solid rgba(255, 255, 255, 0.1)',
                        overflow: 'hidden'
                      }}
                    >
                      <h2 className="accordion-header">
                        <button
                          className={`accordion-button ${activeAccordion !== category.id ? 'collapsed' : ''}`}
                          type="button"
                          onClick={() => setActiveAccordion(activeAccordion === category.id ? '' : category.id)}
                          style={{
                            background: activeAccordion === category.id ? category.color : '#1B1E27',
                            color: '#fff',
                            border: 'none',
                            padding: '20px 24px',
                            fontSize: '18px',
                            fontWeight: '600',
                            boxShadow: 'none',
                            transition: 'all 0.3s ease'
                          }}
                        >
                          <i className={`${category.icon} me-3`} style={{ fontSize: '20px' }}></i>
                          <span>{category.label}</span>
                          <span 
                            className="ms-auto me-3"
                            style={{
                              background: activeAccordion === category.id ? 'rgba(255,255,255,0.2)' : 'rgba(255,255,255,0.1)',
                              padding: '4px 12px',
                              borderRadius: '12px',
                              fontSize: '14px',
                              fontWeight: '600'
                            }}
                          >
                            {category.count}
                          </span>
                        </button>
                      </h2>
                      <div
                        className={`accordion-collapse collapse ${activeAccordion === category.id ? 'show' : ''}`}
                      >
                        <div className="accordion-body" style={{ padding: '24px', background: '#282D41' }}>
                          {renderCategoryContent(category)}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Info Box */}
                <div className="mt-4" style={{
                  background: 'rgba(18, 110, 151, 0.1)',
                  border: '1px solid rgba(18, 110, 151, 0.3)',
                  borderRadius: '10px',
                  padding: '20px',
                }}>
                  <div className="d-flex align-items-start">
                    <i className="fas fa-info-circle me-3 mt-1" style={{ color: '#126E97', fontSize: '20px' }}></i>
                    <div>
                      <h6 className="text-white mb-2">How to Bookmark Content</h6>
                      <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px', marginBottom: 0 }}>
                        Click the bookmark icon <i className="fas fa-bookmark" style={{ color: '#FFA500' }}></i> on any note, spotter, 
                        OSCE case, or quiz question to save it for quick access later. All your bookmarked content will appear here 
                        organized by category in accordion format.
                      </p>
                    </div>
                  </div>
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
