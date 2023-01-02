import React, { useState } from "react";
import Dropdown from "../../Components/Form/Dropdown";
import { Grid, TextField } from "@mui/material";
import { MuiPickersUtilsProvider } from "@material-ui/pickers";
import { DatePicker } from "@material-ui/pickers";

import MomentUtils from "@date-io/moment";
import moment from "moment";

const SearchComponent = (props) => {
 
    

    return (
        <div style={{ display: "flex" }}>
            {!props.inputDestroyer && (
                <Grid>
                    <Dropdown
                        label={"Aktarım Durumu"}
                        options={props.transferStatus}
                        key={props.transferStatus.key}
                        value={props.transferStatus.value}
                        onChange={e=>props.setTransferStatus(e.target.value)}
                    />

                    <Dropdown
                        label={"Firma"}
                        options={props.companyOf}
                        key={props.companyOf.id}
                        value={props.companyOf.company_id}
                        onChange={e=>props.setCompanyOf(e.target.value)}
                    />
                    <Dropdown
                        label={"Tür"}
                        options={props.typeOf}
                        key={props.typeOf.key}
                        value={props.typeOf.value}
                        onChange={e=>props.setTypeOf(e.target.value)}
                    />
                    {/* <DesktopDatePicker
                        label="Date desktop"
                        inputFormat="dd/mm/yyyy"
                        value={value}
                        onChange={handleChange}
                        renderInput={(params) => <TextField {...params} />}
                    /> */}
                    <MuiPickersUtilsProvider utils={MomentUtils}>
                        <DatePicker
                            label={"Başlangıç Tarihi"}
                            // clearable
                            value={props.beginDate}
                            onChange={(e) => props.handleBeginDateChange(e)}
                            format="DD/MM/YYYY"
                        />
                        <br/>
                        <br/>
                        <DatePicker
                            label={"Bitiş Tarihi"}
                            // clearable
                            value={props.endDate}
                            onChange={(e) => props.handleEndDateChange(e)}
                            format="DD/MM/YYYY"
                        />
                    </MuiPickersUtilsProvider>
                </Grid>
            )}
        </div>
    );
};
export default SearchComponent;