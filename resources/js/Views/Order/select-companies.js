import { inject, observer } from "mobx-react";
import React, { useEffect, useState, useRef } from "react";
import Layout from "../../Components/Layout/homeLayout";
import { useForm } from "react-hook-form";
import { useSnackbar } from "notistack";
import swal from "sweetalert";
import { DataGrid } from "@mui/x-data-grid";
import useStyles from "../../Components/style/theme";
import { ListItem, Button } from "@material-ui/core";
import FiberManualRecordIcon from "@mui/icons-material/FiberManualRecord";
import Box from "@mui/material/Box";

function CustomFooterStatusComponent(props) {
    return (
        <Box sx={{ p: 1, display: "flex" }}>
            <FiberManualRecordIcon
                fontSize="small"
                sx={{
                    mr: 1,
                    color:
                        props.selectedRows.length === props.accountLimit
                            ? "#d9182e"
                            : "transparent",
                }}
            />
            {props.selectedRows.length === props.accountLimit
                ? "Kullanıcı Limitine Ulaşıldı."
                : ""}
        </Box>
    );
}
export { CustomFooterStatusComponent };

const SelectCompanies = (props) => {
    const classes = useStyles();
    const [pageSize, setPageSize] = useState(20);
    const { enqueueSnackbar, closeSnackbar } = useSnackbar();
    const { handleSubmit } = useForm();
    const [rowData, setRowData] = useState([]);
    const [selectionModel, setSelectionModel] = useState([]);
    const [selectedRows, setSelectedRows] = useState([]);
    const [done, setDone] = useState(false);
    const [accountLimit, setAccountLimit] = useState(
        props.location.state.accountLimit
    );
    useEffect(() => {
        settingData();
    }, []);

    const settingData = async () => {
        var params = {
            licenseId: props.location.state.licenseId,
            query: [],
        };

        new Promise((resolve) => {
            resolve(
                axios.post(`/api/queryApi/companies`, params, {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                })
            );
        })
            .then((res) => {
                res.data.data.forEach((item, i) => {
                    setRowData((row) => [
                        ...row,
                        { id: i, logoId: item.NR, name: item.NAME },
                    ]);
                });
            })
            .finally(() => setDone(true))
            .catch((e) => console.log(e));
    };

    useEffect(() => {
        const settingSelected = props.location.state?.companiesOfLicense.map(
            (item) => rowData?.find((row) => row.logoId === item.logoId)
        );
        settingSelected.forEach((item) => {
            item && setSelectionModel((row) => [...row, item?.id]);
        });
    }, [props.location.state?.companiesOfLicense, done]);

    const onRowsSelectionHandler = (ids) => {
        setSelectionModel(ids);
        const selectedIDs = new Set(ids);
        const selectedRows = rowData.filter((r) => selectedIDs.has(r.id));
        setSelectedRows(selectedRows);
    };
    const arrayOfSelections = Object.values(selectedRows);
    const submitHandler = () => {
        // console.log("selectedRows", selectedRows);
        let parameters = {
            licenseId: "MNKCF-8HV9R-ALK2D-LHC4B",
            userId: props?.location.state.userId,
            licenseId: props?.location.state.licenseId,
            selectedCompanies: arrayOfSelections,
        };
        axios
            .post(`/api/company/multiStore`, parameters, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            })
            .then((res) => {
                if (res.data.success) {
                    swal(res.data.message);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((err) => {
                enqueueSnackbar(
                    err.res.data ? err.res.data.message : err.message,
                    { variant: "error" }
                );
            });
    };

    return (
        <Layout>
            {rowData && (
                <div
                    container
                    className="selectCompanyTable"
                    // style={{ height: 400, width: "100%" }}
                >
                    <div style={{ display: "flex", height: "100%" }}>
                        <div style={{ flexGrow: 1 }}>
                            <form
                                onSubmit={handleSubmit(submitHandler)}
                                className={classes.form}
                            >
                                <DataGrid
                                    columns={[
                                        {
                                            field: "logoId",
                                            headerName: "Logo ID",
                                        },
                                        {
                                            field: "name",
                                            headerName: "Firma Adı",
                                            flex: 1,
                                        },
                                    ]}
                                    rows={rowData}
                                    // pageSize={15}
                                    // rowsPerPageOptions={[15]}
                                    // pagination
                                    keepNonExistentRowsSelected
                                    autoHeight
                                    checkboxSelection
                                    isRowSelectable={(params) =>
                                        selectedRows.length < accountLimit ||
                                        selectedRows.find(
                                            (row) => row.id === params.id
                                        )
                                    }
                                    onSelectionModelChange={(ids) => {
                                        onRowsSelectionHandler(ids);
                                    }}
                                    selectionModel={selectionModel}
                                    components={{
                                        Footer: CustomFooterStatusComponent,
                                    }}
                                    componentsProps={{
                                        footer: {
                                            selectedRows,
                                            accountLimit,
                                        },
                                    }}
                                    // {...rowData}
                                />
                                <ListItem>
                                    <Button
                                        variant="contained"
                                        type="submit"
                                        fullWidth
                                        color="primary"
                                    >
                                        KAYDET
                                    </Button>
                                </ListItem>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </Layout>
    );
};
export default inject("AuthStore")(observer(SelectCompanies));
