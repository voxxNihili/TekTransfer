import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import DataTable from "react-data-table-component";
import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import swal from "sweetalert";
import moment from "moment";
import { OutTable, ExcelRenderer } from "react-excel-renderer";
import { Paper } from "@material-ui/core";

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
        });
        setSelectedFile(event.target.files[0]);
        setIsSelected(true);
        setDataLoaded(true);
        console.log("resp", resp);
    };
    return (
        <Layout>
            {console.log("selectedFile", selectedFile)}
            {console.log("rows", rows)}
            {console.log("cols", cols)}
            <input type="file" name="file" onChange={changeHandler} />
            {isSelected ? (
                <div>
                    <p>Filename: {selectedFile.name}</p>
                    <p>Filetype: {selectedFile.type}</p>
                    <p>Size in bytes: {selectedFile.size}</p>
                    <p>
                        lastModifiedDate:{" "}
                        {selectedFile.lastModifiedDate.toLocaleDateString()}
                    </p>
                </div>
            ) : (
                <p>Select a file to show details</p>
            )}
            <div>
                <button onClick={handleSubmission}>Submit</button>
            </div>
            {dataLoaded && (
                <Paper>
                    <OutTable
                        data={rows}
                        columns={cols}
                        tableClassName="ExcelTable2007"
                        tableHeaderRowClass="heading"
                    />
                </Paper>
            )}
        </Layout>
    );
};
export default inject("AuthStore")(observer(Index));
