import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import CustomInput from "../../Components/Form/CustomInput";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import CircularProgress from "@mui/material/CircularProgress";
import swal from "sweetalert";
import moment from "moment";
import ExcelList from "./ExcelList";
import { OutTable, ExcelRenderer } from "react-excel-renderer";
import {
    Box,
    Paper,
    Button,
    Input,
    Typography,
    Grid,
    Container,
} from "@material-ui/core";

const Index = (props) => {
    const [accessToken, setAccessToken] = useState();
    const [rows, setRows] = useState([]);
    const [cols, setCols] = useState([]);
    const [dataLoaded, setDataLoaded] = useState(false);
    const [loading, setLoading] = useState(false);
    const [selectedFile, setSelectedFile] = useState();
    const [isSelected, setIsSelected] = useState(false);
    const [isFilePicked, setIsFilePicked] = useState(false);
    // const fileHandler = (event) => {
    //     let fileObj = event?.target?.files[0];
    //     //just pass the fileObj as parameter
    //     ExcelRenderer(fileObj, (err, resp) => {
    //         if (err) {
    //             console.log(err);
    //         } else {
    //             setRows(resp);
    //             setCols(resp);
    //         }
    //     });
    // };
    useEffect(() => {
        setAccessToken(props.AuthStore.appState.user.access_token);
    }, []);
    const handleSubmission = () => {
        const formData = new FormData();
        setLoading(true);
        formData.append("File", selectedFile);
        var params = {
            file: selectedFile,
        };

        axios
            .post(`/api/uploadInvoiceExcel`, formData, {
                headers: {
                    Authorization: "Bearer " + accessToken,
                    "Content-Type": "multipart/form-data",
                    // Accept: "application/json",
                },
            })
            .then((res) => {
                
                console.log("resdata", res);
                // setLoading(false);
                if (res.data.success) {
                    swal({
                        title: "Ba??ar??l??!",
                        text: res.data.message,
                        icon: "success",
                    }).then(() => {
                        window.location.reload(false);
                        setLoading(false);
                    });
                } else {
                    swal({
                        title: "Hata",
                        text: res.data.message,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then(() => {
                        setLoading(false);
                    });;
                }
            })
            .catch((e) => console.log(e));

        // fetch(
        // 	'https://freeimage.host/api/1/upload?key=<YOUR_API_KEY>',
        // 	{
        // 		method: 'POST',
        // 		body: formData,
        // 	}
        // )
        // 	.then((response) => response.json())
        // 	.then((result) => {
        // 		console.log('Success:', result);
        // 	})
        // 	.catch((error) => {
        // 		console.error('Error:', error);
        // 	});
    };
    const changeHandler = (event) => {
        let fileObj = event?.target?.files[0];
        //just pass the fileObj as parameter
        // ExcelRenderer(fileObj, (err, resp) => {
        //     if (err) {
        //         console.log(err);
        //     } else {
        //         setRows(resp.rows);
        //         setCols(resp.cols);
        //     }
        //     console.log("resp", resp);
        // });
        setSelectedFile(event?.target?.files[0]);
        setIsSelected(true);
        setDataLoaded(true);
    };

    return (
        <Layout>
            {loading && (
                <Box sx={{ display: "flex", position: "absolute", left: "50%", top: "50%", zIndex: "999" }}>
                    <CircularProgress />
                </Box>
            )}
            <Button variant="contained" component="label" color="primary">
                Excel Y??kle
                <input
                    hidden
                    type="file"
                    accept=".xlsx, .xls, .csv"
                    onChange={changeHandler}
                />
            </Button>

            {isSelected ? (
                <Box>
                    <Typography
                        variant="body1"
                        color="textPrimary"
                        component="p"
                        style={{ fontWeight: 600 }}
                    >
                        Dosya Ad??: {selectedFile?.name}
                    </Typography>
                    <Typography
                        variant="body1"
                        color="textPrimary"
                        component="p"
                        style={{ fontWeight: 600 }}
                    >
                        Dosya T??r??: {selectedFile?.type}
                    </Typography>
                    <Typography
                        variant="body1"
                        color="textPrimary"
                        component="p"
                        style={{ fontWeight: 600 }}
                    >
                        Dosya Boyutu: {selectedFile?.size}{" "}
                        {selectedFile ? " Bayt" : " "}
                    </Typography>
                    <Typography
                        variant="body1"
                        color="textPrimary"
                        component="p"
                        style={{ fontWeight: 600 }}
                    >
                        Son De??i??tirilme Tarihi:
                        {selectedFile?.lastModifiedDate.toLocaleDateString()}
                    </Typography>
                    <Typography style={{ fontWeight: "600", color: "green" }}>
                        {" "}
                        {selectedFile ? "EXCEL Y??KLEND??" : " "}
                    </Typography>
                </Box>
            ) : (
                <div className="mt-2"></div>
            )}

            <Grid>
                <Button
                    variant="contained"
                    disabled={!isSelected}
                    type="submit"
                    // fullWidth
                    color="primary"
                    onClick={handleSubmission}
                >
                    Excel Aktar
                </Button>
                <div className="mt-3">
                    <ExcelList accessToken={accessToken} />
                </div>
            </Grid>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
