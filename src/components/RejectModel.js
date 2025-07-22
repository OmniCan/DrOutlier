import * as React from 'react';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import { useState } from 'react';
import {
	Grid,
	Stack,
	InputLabel,
	OutlinedInput,
	FormHelperText
} from '@mui/material';

export default function RejectModel({ open, setOpen, handler }) {
	const [reason, setReason] = useState('')
	const [rejectError, setRejectError] = useState({ status: false, text: '' })

	const handleClickOpen = () => {
		if (!reason) return setRejectError({ status: true, text: 'Reason is required' });
		handler(reason);
		setOpen(false)
		setReason('')
	};

	const handleClose = () => {
		setOpen(false);
	};

	return (
		<>
			<Dialog
				open={open}
				onClose={handleClose}
				aria-labelledby="alert-dialog-title"
				aria-describedby="alert-dialog-description"
			>
				<DialogTitle id="alert-dialog-title">
					Reason *
				</DialogTitle>
				<DialogContent  >
					<Grid item xs={12}>
						<Stack spacing={1}>
							<OutlinedInput
								id="reason"
								type="text"
								value={reason}
								name="slug"
								onChange={(e) => setReason(e.target.value)}
								placeholder="Enter reason"
								fullWidth
							/>
							{rejectError.status && (
								<FormHelperText error>{rejectError.text}</FormHelperText>
							)}
						</Stack>
					</Grid>
					<div style={{ display: 'flex', justifyContent: 'space-between', gap: '1rem', marginTop: '1rem' }}>
						<Button variant="contained" onClick={handleClose}>Cancel</Button>
						<Button variant="outlined" color="error" onClick={handleClickOpen} autoFocus>Reject</Button>
					</div>
				</DialogContent>
				{/* <DialogActions></DialogActions> */}
			</Dialog>
		</>
	);
}

// jbfsh

// nds?