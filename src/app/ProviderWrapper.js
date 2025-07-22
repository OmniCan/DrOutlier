'use client';
import PropTypes from 'prop-types';
import ScrollTop from "@/components/ScrollTop";
import { GoogleOAuthProvider } from "@react-oauth/google";
import { ToastContainer } from "react-toastify";

const ProviderWrapper = ({ children }) => {
  // const GOOGLE_CLIENT_ID = '1:608770434427:web:133e76bf411498880509de'
  const GOOGLE_CLIENT_ID = '90424675581-0d8lrekuq379coa2fpb9vhudttvs6k09.apps.googleusercontent.com'

  return (

    <>
      <GoogleOAuthProvider clientId={GOOGLE_CLIENT_ID}>
        {/* <ScrollTop> */}
        <ToastContainer />
        {children}
        {/* </ScrollTop> */}
      </GoogleOAuthProvider>
    </>
  );
};

ProviderWrapper.propTypes = {
  children: PropTypes.node
};

export default ProviderWrapper;
