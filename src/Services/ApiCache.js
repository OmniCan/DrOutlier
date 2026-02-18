/**
 * Simple API response cache with TTL (Time To Live)
 * Prevents repeated API calls and helps avoid rate limiting
 */

const cache = new Map();
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

export const ApiCache = {
  /**
   * Get cached data for a key
   * @param {string} key - Cache key
   * @returns {any|null} - Cached data or null if expired/not found
   */
  get(key) {
    const item = cache.get(key);
    
    if (!item) {
      return null;
    }
    
    // Check if cache has expired
    if (Date.now() > item.expiry) {
      cache.delete(key);
      return null;
    }
    
    return item.data;
  },

  /**
   * Set cache data with TTL
   * @param {string} key - Cache key
   * @param {any} data - Data to cache
   * @param {number} ttl - Time to live in milliseconds (default: 5 minutes)
   */
  set(key, data, ttl = CACHE_TTL) {
    cache.set(key, {
      data,
      expiry: Date.now() + ttl
    });
  },

  /**
   * Clear specific cache key
   * @param {string} key - Cache key to clear
   */
  clear(key) {
    cache.delete(key);
  },

  /**
   * Clear all cache
   */
  clearAll() {
    cache.clear();
  },

  /**
   * Check if key exists and is not expired
   * @param {string} key - Cache key
   * @returns {boolean}
   */
  has(key) {
    return this.get(key) !== null;
  }
};

export default ApiCache;
