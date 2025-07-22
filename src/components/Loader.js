import ContentLoader from "react-content-loader";
import * as React from 'react';
import Backdrop from '@mui/material/Backdrop';
import CircularProgress from '@mui/material/CircularProgress';
import Button from '@mui/material/Button';

const Loader = () => {

    const [open, setOpen] = React.useState(false);
    // const handleClose = () => {
    //     setOpen(false);
    // };
    // const handleOpen = () => {
    //     setOpen(true);
    // };


    React.useEffect(()=>{
        setOpen(true);
    },[])
    
    return (
        <>

            <div className="loader-container">
                <img
                    src="/images/Header-Logo.webp"
                    className="img-fluid"
                    alt="Radiology"
                />

            </div>

            <div>
                <Backdrop
                    sx={(theme) => ({ color: '#fff', zIndex: theme.zIndex.drawer + 1 })}
                    open={open}
                    // onClick={handleClose}
                >
                    <CircularProgress color="inherit" />
                </Backdrop>
            </div>

            {/* Inline CSS */}
            <style jsx>{`
                .loader-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                 
                    width: 100%;
                    height: 500px;
              
                }

             
            `}</style>
        </>
    );
};

export default Loader;
