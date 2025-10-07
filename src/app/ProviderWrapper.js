'use client';
import PropTypes from 'prop-types';
import ScrollTop from "@/components/ScrollTop";
import { GoogleOAuthProvider } from "@react-oauth/google";
import { ToastContainer } from "react-toastify";

const ProviderWrapper = ({ children }) => {
  // const GOOGLE_CLIENT_ID = '1:608770434427:web:133e76bf411498880509de'
  const GOOGLE_CLIENT_ID = '702468795931-lhpkas4tj0ipau7nj15pna3c8s6etnt2.apps.googleusercontent.com'

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
