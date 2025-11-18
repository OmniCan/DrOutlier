'use client'
import { useState, useEffect } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';
import baseUrl from '@/Services/BaseUrl';

export const useNavigation = () => {
    const [navigationItems, setNavigationItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchNavigation();
    }, []);

    const fetchNavigation = async () => {
        try {
            setLoading(true);
            const token = Cookies.get('user-token');
            
            // Use authenticated endpoint if logged in, public otherwise
            const endpoint = token ? '/api/navigation' : '/api/navigation/public';
            
            const response = await axios.get(`${baseUrl}${endpoint}`, {
                headers: token ? {
                    'Authorization': `Bearer ${token}`
                } : {}
            });

            if (response.data.status === 'success') {
                setNavigationItems(response.data.data.navigation);
            }
        } catch (err) {
            console.error('Error fetching navigation:', err);
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    return { navigationItems, loading, error, refetch: fetchNavigation };
};

export default useNavigation;
