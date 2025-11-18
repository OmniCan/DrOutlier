import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

/**
 * Hook to check if user has access to a specific module
 * @param {string} moduleSlug - The slug of the module to check (e.g., 'spotters', 'osce')
 * @returns {object} { hasAccess, loading, subscription, checkAccess }
 */
export const useModuleAccess = (moduleSlug) => {
  const router = useRouter();
  const [hasAccess, setHasAccess] = useState(null);
  const [loading, setLoading] = useState(true);
  const [subscription, setSubscription] = useState(null);

  const checkAccess = async () => {
    try {
      setLoading(true);
      
      // Get user token from your auth system
      const token = localStorage.getItem('userToken'); // Adjust based on your auth implementation
      
      if (!token) {
        setHasAccess(false);
        setLoading(false);
        return;
      }

      const response = await fetch('https://admin.droutlier.com/api/subscription/check-access', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ module_slug: moduleSlug }),
      });

      const data = await response.json();

      if (data.status === 'success') {
        setHasAccess(data.data.has_access);
      } else {
        setHasAccess(false);
      }
    } catch (error) {
      console.error('Error checking module access:', error);
      setHasAccess(false);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (moduleSlug) {
      checkAccess();
    }
  }, [moduleSlug]);

  return { hasAccess, loading, subscription, checkAccess };
};

/**
 * Hook to get user's active subscription
 * @returns {object} { subscription, loading, refetch }
 */
export const useSubscription = () => {
  const [subscription, setSubscription] = useState(null);
  const [loading, setLoading] = useState(true);

  const fetchSubscription = async () => {
    try {
      setLoading(true);
      const token = localStorage.getItem('userToken');
      
      if (!token) {
        setSubscription(null);
        setLoading(false);
        return;
      }

      const response = await fetch('https://admin.droutlier.com/api/subscription/my-subscription', {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      const data = await response.json();

      if (data.status === 'success' && data.data.has_subscription) {
        setSubscription(data.data.subscription);
      } else {
        setSubscription(null);
      }
    } catch (error) {
      console.error('Error fetching subscription:', error);
      setSubscription(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSubscription();
  }, []);

  return { subscription, loading, refetch: fetchSubscription };
};

/**
 * Component to protect module routes
 */
export const ModuleAccessGuard = ({ moduleSlug, children, fallback = null }) => {
  const { hasAccess, loading } = useModuleAccess(moduleSlug);
  const router = useRouter();

  useEffect(() => {
    if (!loading && hasAccess === false) {
      // Redirect to pricing or show upgrade message
      router.push('/pricing');
    }
  }, [hasAccess, loading]);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (!hasAccess) {
    return fallback || (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-center">
          <h2 className="text-2xl font-bold mb-4">Subscription Required</h2>
          <p className="text-gray-600 mb-6">
            You need an active subscription to access this content.
          </p>
          <button
            onClick={() => router.push('/pricing')}
            className="btn btn-primary"
          >
            View Plans
          </button>
        </div>
      </div>
    );
  }

  return <>{children}</>;
};

export default ModuleAccessGuard;
