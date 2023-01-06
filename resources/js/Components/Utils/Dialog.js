import * as React from "react";
import { Button } from "@material-ui/core";
import FilterListIcon from '@material-ui/icons/FilterList';
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";

export default function AlertDialog(props) {
    return (
        <div>
            <Button
                variant="contained"
                startIcon={<FilterListIcon />}
                component="label"
                color="primary"
                onClick={props.handleClickOpen}
            >
                {props.buttonName}
            </Button>
            <Dialog
                open={props.open}
                onClose={props.handleClose}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {props.dialogTitle}
                </DialogTitle>
                <DialogContent>
                    <DialogContentText id="alert-dialog-description">
                        {props.dialogContentText}
                    </DialogContentText>
                </DialogContent>
                <DialogActions>{props.actions}</DialogActions>
            </Dialog>
        </div>
    );
}
