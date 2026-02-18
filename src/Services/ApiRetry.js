/**
 * API Retry utility with exponential backoff
 * Handles rate limiting and temporary API failures
 */

/**
 * Make an API request with automatic retry on rate limit (429) or server errors (5xx)
 * @param {Function} requestFn - Async function that makes the API request
 * @param {Object} options - Retry options
 * @param {number} options.maxRetries - Maximum number of retries (default: 3)
 * @param {number} options.initialDelay - Initial delay in ms (default: 1000)
 * @param {number} options.maxDelay - Maximum delay in ms (default: 10000)
 * @param {Function} options.onRetry - Callback when retrying
 * @returns {Promise} - API response
 */
export async function apiRequestWithRetry(requestFn, options = {}) {
  const {
    maxRetries = 3,
    initialDelay = 1000,
    maxDelay = 10000,
    onRetry = null
  } = options;

  let lastError;
  let delay = initialDelay;

  for (let attempt = 0; attempt <= maxRetries; attempt++) {
    try {
      const response = await requestFn();
      return response;
    } catch (error) {
      lastError = error;
      
      // Check if it's a retryable error
      const status = error.response?.status;
      const isRateLimited = status === 429;
      const isServerError = status >= 500 && status < 600;
      const isNetworkError = !error.response;
      
      // Don't retry on client errors (4xx except 429)
      if (status >= 400 && status < 500 && status !== 429) {
        throw error;
      }
      
      // If it's the last attempt, throw the error
      if (attempt === maxRetries) {
        throw error;
      }
      
      // Only retry on rate limit, server errors, or network errors
      if (isRateLimited || isServerError || isNetworkError) {
        // Get retry-after header if present (for 429 responses)
        const retryAfter = error.response?.headers['retry-after'];
        const waitTime = retryAfter 
          ? parseInt(retryAfter) * 1000 
          : Math.min(delay, maxDelay);
        
        if (onRetry) {
          onRetry(attempt + 1, waitTime, error);
        }
        
        console.log(`API request failed (attempt ${attempt + 1}/${maxRetries}). Retrying in ${waitTime}ms...`);
        
        // Wait before retrying
        await new Promise(resolve => setTimeout(resolve, waitTime));
        
        // Exponential backoff for next attempt
        delay *= 2;
      } else {
        // Not a retryable error
        throw error;
      }
    }
  }
  
  throw lastError;
}

export default apiRequestWithRetry;
