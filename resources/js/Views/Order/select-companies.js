import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
// import DataTable from "react-data-table-component";
// import SubHeaderComponent from "../../Components/Form/SubHeaderComponent";
// import ExpandedComponent from "../../Components/Form/ExpandedComponent";
import { useForm } from "react-hook-form";
import { useSnackbar } from "notistack";
import swal from "sweetalert";
import { DataGrid } from "@mui/x-data-grid";
import useStyles from "../../Components/style/theme";
import PropTypes from "prop-types";

import { ListItem, Button, TablePagination } from "@material-ui/core";

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
// CustomFooterStatusComponent.propTypes = {
//     status: PropTypes.oneOf(["connected", "disconnected"]).isRequired,
// };
export { CustomFooterStatusComponent };
const SelectCompanies = (props) => {
    const classes = useStyles();
    const [pageSize, setPageSize] = useState(20);
    // const { redirect } = router.query;
    const { enqueueSnackbar, closeSnackbar } = useSnackbar();
    const { handleSubmit } = useForm();
    const [rowData, setRowData] = useState([]);
    const [accountLimit, setAccountLimit] = useState(
        props.location.state.accountLimit
    );
    useEffect(() => {
        settingData();
    }, []);

    const settingData = async () => {
        var params = {
            license: props.location.state.orderId,
            query: [],
        };
        await axios
            .post(`/api/queryApi/companies`, params, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                res.data.data.forEach((item, i) => {
                    setRowData((row) => [
                        ...row,
                        {
                            id: i,
                            logoId: item.NR,
                            name: item.NAME,
                        },
                    ]);
                });
            })
            .catch((e) => console.log(e));
    };
    const [selectedRows, setSelectedRows] = useState([]);
    const onRowsSelectionHandler = (ids) => {
        const selectedRowsData = ids.map((id) =>
            rowData.find((row) => row.id === id)
        );
        setSelectedRows(selectedRowsData);
    };
    const arrayOfSelections = Object.values(selectedRows);
    const submitHandler = () => {
        console.log("selectedRows", selectedRows);
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
            {console.log("props", props)}
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
                                    components={{
                                        Footer: CustomFooterStatusComponent,
                                    }}
                                    componentsProps={{
                                        footer: { selectedRows, accountLimit },
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
