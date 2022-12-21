import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import CustomInput from "../../Components/Form/CustomInput";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import moment from "moment";
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
    const [rows, setRows] = useState([]);
    const [cols, setCols] = useState([]);
    const [dataLoaded, setDataLoaded] = useState(false);
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
    const handleSubmission = () => {
        const formData = new FormData();

        formData.append("File", selectedFile);
        var params = {
            file: selectedFile,
        };

        axios
            .post(`/api/uploadInvoiceExcel`, formData, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                    "Content-Type": "multipart/form-data",
                    // Accept: "application/json",
                },
            })
            .then((res) => {
                // setData(res);
                console.log("resdata", res);
                // setLoading(false);
                if(res.data.success){
                    swal({
                        title: "Başarılı!",
                        text: res.data.message,
                        icon: "success",
                      }).then(() => {
                        window.location.reload(false)
                      });
                }
                else
                {
                    swal({
                        title: "Hata",
                        text: res.data.message,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                      })
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
        ExcelRenderer(fileObj, (err, resp) => {
            if (err) {
                console.log(err);
            } else {
                setRows(resp.rows);
                setCols(resp.cols);
            }
            console.log("resp", resp);
        });
        setSelectedFile(event.target.files[0]);
        setIsSelected(true);
        setDataLoaded(true);
   
    };
    return (
        <Layout>
            <Container>
             
                    <Button
                        variant="contained"
                        component="label"
                        color="primary"
                    >
                        Excel Yükle
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
                                Dosya Adı: {selectedFile.name}
                            </Typography>
                            <Typography
                                variant="body1"
                                color="textPrimary"
                                component="p"
                                style={{ fontWeight: 600 }}
                            >
                                Dosya Türü: {selectedFile.type}
                            </Typography>
                            <Typography
                                variant="body1"
                                color="textPrimary"
                                component="p"
                                style={{ fontWeight: 600 }}
                            >
                                Dosya Boyutu: {selectedFile.size} bayt
                            </Typography>
                            <Typography
                                variant="body1"
                                color="textPrimary"
                                component="p"
                                style={{ fontWeight: 600 }}
                            >
                                Son Değiştirilme Tarihi:
                                {selectedFile.lastModifiedDate.toLocaleDateString()}
                            </Typography>
                            <Typography
                                style={{ fontWeight: "600", color: "green" }}
                            >
                                {" "}
                                EXCEL YÜKLENDİ
                            </Typography>
                        </Box>
                    ) : (
                        <Typography>Dosya Detayları:</Typography>
                    )}
           
                <Grid>
                    <Button
                        variant="contained"
                        type="submit"
                        // fullWidth
                        color="primary"
                        onClick={handleSubmission}
                    >
                        Fatura Aktar
                    </Button>
                </Grid>
                {/* {dataLoaded && (
                <Paper>
                    <OutTable
                        data={rows}
                        columns={cols}
                        tableClassName="ExcelTable2007"
                        tableHeaderRowClass="heading"
                    />
                </Paper>
            )} */}
            </Container>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
