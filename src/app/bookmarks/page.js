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
  const [activeTab, setActiveTab] = useState('notes');

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
      
      console.log('Fetching bookmarks with userId:', userId, 'token:', token ? 'exists' : 'missing');
      
      // Fetch all bookmark types in parallel
      const results = await Promise.allSettled([
        axios.post(`${baseUrl}/api/note/get-note-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/spotters/get-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/osce/get-osce-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/quiz/bookmarks`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/category-munchie/get-munchie-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/basic-category/get-basic-bookmark`, { user_id: userId }, config),
        axios.post(`${baseUrl}/api/watch-and-learn-category/get-watch-bookmark`, { user_id: userId }, config)
      ]);

      console.log('Bookmark results:', results);

      // Parse results and handle different response structures
      const parseBookmarkData = (result, index) => {
        if (result.status !== 'fulfilled') {
          console.log(`API ${index} failed:`, result.reason?.response?.data || result.reason);
          return [];
        }
        
        const response = result.value.data;
        console.log(`API ${index} response:`, response);
        console.log(`API ${index} response.data:`, response.data);
        
        // Handle different response structures
        if (Array.isArray(response)) {
          return response;
        }
        
        if (response.data) {
          // Check for nested list property (Laravel paginated response)
          if (response.data.list) {
            // Laravel pagination object with data array
            if (response.data.list.data && Array.isArray(response.data.list.data)) {
              console.log(`API ${index} - Found paginated data in response.data.list.data:`, response.data.list.data);
              return response.data.list.data;
            }
            // Direct array in list
            if (Array.isArray(response.data.list)) {
              console.log(`API ${index} - Found array in response.data.list:`, response.data.list);
              return response.data.list;
            }
          }
          
          // Check if data itself is an array
          if (Array.isArray(response.data)) {
            return response.data;
          }
        }
        
        if (response.bookmarks && Array.isArray(response.bookmarks)) {
          return response.bookmarks;
        }
        
        console.log(`API ${index} - Could not parse data, returning empty array`);
        return [];
      };

      setBookmarks({
        notes: parseBookmarkData(results[0], 0),
        spotters: parseBookmarkData(results[1], 1),
        osce: parseBookmarkData(results[2], 2),
        quizora: parseBookmarkData(results[3], 3),
        aiRad: parseBookmarkData(results[4], 4),
        practicalEssentials: parseBookmarkData(results[5], 5),
        watchAndLearn: parseBookmarkData(results[6], 6)
      });
    } catch (error) {
      console.error('Error fetching bookmarks:', error);
      console.error('Error details:', error.response?.data);
      console.error('Error status:', error.response?.status);
      
      // Don't show error toast for 403 - it just means no access to that module
      if (error.response?.status !== 403) {
        toast.error(error.response?.data?.message || 'Failed to load bookmarks');
      }
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

  // Helper function to generate correct URL based on type
  const getViewUrl = (item, type) => {
    // Different modules use different field names for category
    let categoryId;
    
    switch(type) {
      case 'notes':
        // Notes (Blog model) use 'category' field
        categoryId = item.category || 100;
        return `/notes/view?id=${categoryId}&noteId=${item.id}#page1`;
      case 'spotters':
        // Spotters use 'category' or 'category_id' field
        categoryId = item.category || item.category_id || item.parent_id || 100;
        return `/spotters/view?id=${categoryId}&spotterId=${item.id}#page1`;
      case 'osce':
        // OSCE use 'category' field
        categoryId = item.category || item.parent_id || item.category_id || 100;
        return `/osce/view?id=${categoryId}&osceId=${item.id}#page1`;
      case 'quizora':
        return `/quiz?id=${item.id}`;
      case 'aiRad':
        // AI Rad (Munchie model) use 'category' field
        categoryId = item.category || item.parent_id || item.category_id || 100;
        return `/ai-rad/view?id=${categoryId}&munchieId=${item.id}#page1`;
      case 'practicalEssentials':
        // Practical Essentials (Basic model) use 'category' field
        categoryId = item.category || item.parent_id || item.category_id || 100;
        return `/practical-essentials/view?id=${categoryId}&basicId=${item.id}#page1`;
      case 'watchAndLearn':
        // Watch and Learn doesn't have individual view pages, link to main page
        return `/watch-and-learn`;
      default:
        return `/${type}/${item.id}`;
    }
  };

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
              href={getViewUrl(item, type)}
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
                {/* Tabs Navigation */}
                <div className="mb-4">
                  <div className="d-flex gap-2 flex-wrap">
                    {categories.map((tab) => (
                      <button
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        className="btn"
                        style={{
                          background: activeTab === tab.id 
                            ? tab.color 
                            : 'rgba(255, 255, 255, 0.1)',
                          color: '#fff',
                          border: 'none',
                          padding: '12px 24px',
                          borderRadius: '8px',
                          fontWeight: '500',
                          transition: 'all 0.3s ease',
                          display: 'flex',
                          alignItems: 'center',
                          gap: '8px'
                        }}
                      >
                        <i className={tab.icon}></i>
                        <span>{tab.label}</span>
                        <span 
                          style={{
                            background: activeTab === tab.id ? 'rgba(255,255,255,0.3)' : 'rgba(255,255,255,0.2)',
                            padding: '2px 8px',
                            borderRadius: '10px',
                            fontSize: '12px',
                            fontWeight: '600',
                            marginLeft: '4px'
                          }}
                        >
                          {tab.count}
                        </span>
                      </button>
                    ))}
                  </div>
                </div>

                {/* Tab Content */}
                <div style={{
                  background: '#282D41',
                  borderRadius: '15px',
                  padding: '30px',
                  border: '1px solid rgba(255, 255, 255, 0.1)',
                  minHeight: '400px'
                }}>
                  {renderCategoryContent(categories.find(c => c.id === activeTab))}
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
                        organized by category.
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
