import { inject, observer } from "mobx-react";
import React, { useEffect, useState } from "react";
import Layout from "../../Components/Layout/homeLayout";
import moment from "moment";
import { Formik } from "formik";
import * as Yup from "yup";
import CustomInput from "../../Components/Form/CustomInput";
import Select from "react-select";
import ImageUploader from "react-images-upload";
import CKEditor from "ckeditor4-react";
import Dropdown from "../../Components/Form/Dropdown";
import swal from "sweetalert";
import axios from "axios";
import { difference, values } from "lodash";
import { Form } from "react-bootstrap";
import DynamicTable from "./table";
import { Button } from "@material-ui/core";

import {
    Input,
    List,
    ListItem,
    Paper,
    TextField,
    Typography,
} from "@mui/material";
import useStyles from "../../Components/style/theme";

const ReportIndex = (props) => {
    const { params } = props.match;
    const classes = useStyles();
    const [loading, setLoading] = useState(true);
    const [submitDisabler, setSubmitDisabler] = useState(false);
    const [table, setTable] = useState(false);
    const [licenseFlag, setLicenseFlag] = useState(false);
    const [companyFlag, setCompanyFlag] = useState(false);
    const [queries, setQueries] = useState([]);
    const [formData, updateFormData] = useState([]);
    const [dataTable, setDataTable] = useState([]);
    const [selectedData, setSelectedData] = useState([
        // { licenseId: "", companyId: "", periodId: "", companyQueryId: "" },
    ]);
    const [licenseOptions, setLicenseOptions] = useState([
        { label: "Lisansınızı Seçiniz", value: "0", key: "9999" },
    ]);
    const [companyOptions, setCompanyOptions] = useState([
        { label: "Şirketinizi Seçiniz", value: "0", key: "999" },
    ]);
    const [periodOptions, setPeriodOptions] = useState([
        { label: "Aralık Seçiniz", value: "0", key: "99" },
    ]);

    const settingData = async () => {
        await axios
            .get(`/api/report/${params.id} `, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    setQueries(res.data.data[0].query_param);

                    let newList = res.data.licenses.map((item) => {
                        return {
                            label: item?.licenseKey,
                            key: item?.id,
                            value: item?.id,
                        };
                    });
                    newList.unshift({
                        label: "Lisansınızı Seçiniz",
                        value: "0",
                        key: "9999",
                    });
                    setLicenseOptions(newList);

                    setLoading(false);
                } else {
                    swal(res.data.message);
                }
            })
            .catch((e) => console.log(e));
    };
    useEffect(() => {
        settingData();
    }, []);

    useEffect(() => {
        setPeriodOptions;
        axios
            .get(`/api/query/showLogoCompanies/${selectedData.licenseId}`, {
                headers: {
                    Authorization:
                        "Bearer " + props.AuthStore.appState.user.access_token,
                },
            })
            .then((res) => {
                if (res.data.success) {
                    let newList = res.data.companies.map((item) => {
                        return {
                            label: item?.name,
                            key: item?.id,
                            value: item?.logoId,
                        };
                    });
                    newList.unshift({
                        label: "Şirketinizi Seçiniz",
                        value: "0",
                        key: "999",
                    });
                    setCompanyOptions(newList);

                    // setLicenseFlag(true);
                    setLoading(false);
                } else {
                    swal(res.data.message);
                    // setLicenseFlag(false);
                }
            })
            .catch((e) => console.log(e));
    }, [licenseFlag]);

    useEffect(() => {
        let defaultOption = {
            label: "Aralık Seçiniz",
            value: "0",
            key: "99",
        };
        setPeriodOptions(defaultOption);
        selectedData.companyQueryId !== 0
            ? axios
                  .get(
                      `/api/query/showLogoPeriods/${selectedData.companyQueryId}`,
                      {
                          headers: {
                              Authorization:
                                  "Bearer " +
                                  props.AuthStore.appState.user.access_token,
                          },
                      }
                  )
                  .then((res) => {
                      if (res.data.success) {
                          let newList = res.data.logoCompanyPeriods.map(
                              (item, idx) => {
                                  return {
                                      label:
                                          moment(item.BEGDATE).format(
                                              "DD.MM.YYYY"
                                          ) +
                                          " - " +
                                          moment(item.ENDDATE).format(
                                              "DD.MM.YYYY"
                                          ),
                                      key: idx,
                                      value: item?.NR,
                                  };
                              }
                          );
                          newList.unshift(defaultOption);

                          setPeriodOptions(newList);

                          setLoading(false);
                      } else {
                          swal(res.data.message);
                      }
                  })
                  .catch((e) => console.log(e))
            : setPeriodOptions(defaultOption);
    }, [licenseFlag, companyFlag]);

    const handleLicenseDropdown = (e) => {
        setSelectedData({
            ...selectedData,
            licenseId: e.target.value,
            companyId: 0,
            periodId: 0,
            companyQueryId: 0,
        });
        setLicenseFlag((current) => !current);
    };
    const handleCompanyDropdown = (e, companyId) => {
        var idx = e.target.options.selectedIndex;
        setSelectedData({
            ...selectedData,
            companyId: e.target.value,
            companyQueryId: companyId[idx].key,
        });
        setCompanyFlag((current) => !current);
    };
    const handlePeriodDropdown = (e) => {
        setSelectedData({ ...selectedData, periodId: e.target.value });
    };

    const handleChange = (e) => {
        updateFormData({ ...formData, [e.target.id]: e.target.value.trim() });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        const data = new FormData();
        data.append("license", "MNKCF-8HV9R-ALK2D-LHC4B");
        // data.append("query", JSON.stringify(formData));
        const arrayOfFormFields = Object.values(formData);
        const arrayOfSelections = Object.values(selectedData);
        arrayOfSelections.includes(0) ||
        arrayOfFormFields.includes(0) ||
        arrayOfFormFields.length !== queries.length
            ? swal("Bütün Alanları Doldurunuz")
            : setSubmitDisabler(false)
        console.log("a", arrayOfSelections.includes(0));
        console.log("b", arrayOfFormFields.includes(0));
        console.log("c", arrayOfFormFields.length !== queries.length);
        const config = {
            headers: {
                Accept: "application/json",
                "content-type": "application/json",
                Authorization:
                    "Bearer " + props.AuthStore.appState.user.access_token,
            },
        };

        let parameters = {
            licenseId: selectedData.licenseId,
            companyId: selectedData.companyId,
            periodId: selectedData.periodId,
            query: formData,
        };
        axios
            .post(`/api/queryApi/${params.id}`, parameters, config)
            .then((res) => {
                if (res.data.data.length > 0) {
                    setDataTable(res.data.data);
                    setTable(true);
                    setLoading(false);
                } else {
                    swal("Geçersiz Parametre");
                }
            })
            .catch((e) => console.log(e));
    };

    if (loading) return <div>Yükleniyor</div>;

    return (
        <Layout>
            <Form>
                <Paper>
                    <List sx={{ display: "flex" }}>
                        <ListItem sx={{ justifyContent: "center" }}>
                            <Dropdown
                                label={"Lisans"}
                                options={licenseOptions}
                                key={licenseOptions.key}
                                value={licenseOptions.value}
                                onChange={handleLicenseDropdown}
                            />
                        </ListItem>

                        {companyOptions.length > 1 && (
                            <ListItem sx={{ justifyContent: "center" }}>
                                <Dropdown
                                    label={"Şirket"}
                                    options={companyOptions}
                                    key={companyOptions.id}
                                    dataTag={companyOptions.id}
                                    value={companyOptions.value}
                                    onChange={(e) =>
                                        handleCompanyDropdown(e, companyOptions)
                                    }
                                />
                            </ListItem>
                        )}
                        {periodOptions.length > 1 && (
                            <ListItem sx={{ justifyContent: "center" }}>
                                <Dropdown
                                    label={"Period"}
                                    options={periodOptions}
                                    key={periodOptions.id}
                                    value={periodOptions.value}
                                    onChange={handlePeriodDropdown}
                                />
                            </ListItem>
                        )}
                    </List>
                    <List sx={{ display: "flex" }}>
                        {queries.map((item) => (
                            <ListItem
                                sx={{
                                    display: "flex",
                                    flexDirection: "column",
                                }}
                            >
                                <Typography className={classes.brand}>
                                    {item.parameter[0].name}
                                </Typography>
                                <TextField
                                    name={item.parameter[0]?.name}
                                    id={item.parameter[0]?.parameter}
                                    type={item.parameter[0]?.data_type}
                                    onChange={handleChange}
                                />
                            </ListItem>
                        ))}
                    </List>
                    {/* {submitDisabler && (
                        <p className={classes.loginError}>
                            Bütün Alanları Doldurunuz
                        </p>
                    )} */}
                    <Button
                        // disabled={selectedData}
                        variant="contained"
                        type="submit"
                        fullWidth
                        color="primary"
                        onClick={handleSubmit}
                        // className="col-4"
                    >
                        Raporla
                    </Button>
                </Paper>
            </Form>

            {table == true && (
                <div className="row" style={{ marginTop: "10px" }}>
                    <DynamicTable dataTable={dataTable} />
                </div>
            )}
        </Layout>
    );
};
export default inject("AuthStore")(observer(ReportIndex));
