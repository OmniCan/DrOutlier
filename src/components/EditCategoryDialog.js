"use client"
import React, { useEffect, useState } from 'react';
import { useTheme } from '@mui/material/styles';
import useMediaQuery from '@mui/material/useMediaQuery';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import * as yup from 'yup';
import { useFormik } from 'formik';
import slugify from 'slugify/slugify';
import { openSnackbar } from 'api/snackbar';
import { useRouter } from 'next/navigation';
import { Grid, Stack, TextField, InputLabel } from '@mui/material';
import MainCard from 'components/MainCard';
import AnimateButton from 'components/@extended/AnimateButton';
import axios from 'axios';
import baseUrl from 'Services/BaseUrl';
import Cookies from 'js-cookie';


const validationSchema = yup.object({
  name: yup.string().required('Category Name is Required'),
  priority: yup.string().required('Priority is Required'),

});

const EditPropertyType = ({ open, onClose, data, fetchData, URL , setUpdateCount }) => {
  const theme = useTheme();
  const fullScreen = useMediaQuery(theme.breakpoints.down('md'));

  const [displayImage, setDisplayImage] = useState();
  const [displayImageUrl, setDisplayImageUrl] = useState('');

  useEffect(() => {
    if (data) {
      formik.setValues({
        name: data.name || '',
        priority: data.priority || '',
      });
      if (data.image) setDisplayImageUrl(baseUrl + '/' + data?.image)
    }
  }, [data]);


  const formik = useFormik({
    initialValues: {
      name: '',
      priority: '',
    },
    validationSchema,
    onSubmit: async (values) => {
      try {

        const formData = new FormData();
        formData.append('id', data._id);
        formData.append('name', values.name);
        formData.append('priority', values.priority);


        if (displayImage) formData.append('image', displayImage);

        const response = await axios.patch(`${baseUrl}/api/admin/niche`, formData, {
          headers: { 'Authorization': `Bearer ${Cookies.get('admin-token')}`, }
        });

        // fetchData();
        setDisplayImageUrl(null);
        setDisplayImage(null);


        setUpdateCount(prev => prev + 1)


        openSnackbar({
          open: true,
          message: response.data.message,
          variant: 'alert',
          alert: {
            color: 'success'
          }
        });

        onClose();

      } catch (error) {
        console.error('Error:', error);
        openSnackbar({
          open: true,
          message: error.response.data.message,
          variant: 'alert',
          alert: {
            color: 'error'
          }
        });
      }
    }
  });

  const generateSlug = (value) => {
    let slug = slugify(value, {
      lower: true,
      remove: /[*+~.()'"!:@]/g,
      replacement: '-',
      strict: false,
      locale: 'vi',
      trim: true
    });
    formik.setFieldValue('slugname', slug);
  };

  const handleDisplayImageChange = (event) => {
    const file = event.target.files[0];
    setDisplayImage(file);
    // setDisplayImageUrl(URL.createObjectURL(file));
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setDisplayImageUrl(reader.result);
      }
      reader.readAsDataURL(file);
    }
  };


  return (
    <Dialog fullScreen={fullScreen} open={open} onClose={onClose}>
      <DialogTitle>Edit Category</DialogTitle>
      <form onSubmit={formik.handleSubmit} style={{ padding: '0px 20px 10px 20px' }}>
        <DialogContent>
          <Grid container spacing={10}>
            <Grid item xs={12}>
              <Grid container spacing={2} alignItems="center">
                <Grid item xs={12} lg={6}>
                  <Stack spacing={1}>
                    <InputLabel htmlFor="name">Name</InputLabel>
                    <TextField
                      fullWidth
                      id="name"
                      name="name"
                      placeholder="Enter name"
                      value={formik.values.name}
                      onChange={formik.handleChange}
                      error={formik.touched.name && Boolean(formik.errors.name)}
                      helperText={formik.touched.name && formik.errors.name}
                    />
                  </Stack>
                </Grid>


                <Grid item xs={12} lg={6}>
                  <Stack spacing={1}>
                    <InputLabel htmlFor="slugname">Priority</InputLabel>
                    <TextField
                      fullWidth
                      id="priority"
                      name="priority"
                      placeholder="Enter slugname"
                      value={formik.values.priority}
                      onChange={formik.handleChange}
                      error={formik.touched.priority && Boolean(formik.errors.priority)}
                      helperText={formik.touched.priority && formik.errors.priority}
                    />
                  </Stack>
                </Grid>


                <Grid item xs={6}>
                  <Stack spacing={1}>
                    <InputLabel htmlFor="property_display_image">
                      <Stack gap={1} xs={6} >
                        Image
                        <Button size='large' style={{ pointerEvents: 'none', padding: '0.9rem' }} variant="outlined">
                          Select Image
                        </Button>
                      </Stack>
                    </InputLabel>
                    <input
                      accept="image/*"
                      id="property_display_image"
                      style={{ display: 'none' }}
                      name="property_display_image"
                      type="file"
                      onChange={handleDisplayImageChange}
                    />
                    {displayImageUrl && (
                      <div style={{ position: 'relative', width: '200px', height: '100px', overflow: 'hidden' }}>
                        <img
                          src={displayImageUrl}
                          alt=" Image"
                          style={{ height: '100%' }}
                        />
                      </div>
                    )}
                    {formik.touched.image && formik.errors.image && (
                      <div style={{ color: 'red', marginTop: '0.5rem' }}>{formik.errors.image}</div>
                    )}
                  </Stack>
                </Grid>


              </Grid>
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Close</Button>
          <AnimateButton>
            <Button variant="contained" type="submit">
              Update
            </Button>
          </AnimateButton>
        </DialogActions>
      </form>
    </Dialog>
  );
};

export default EditPropertyType;
