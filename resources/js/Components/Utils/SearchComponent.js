import React from "react";
import Dropdown from "../Form/Dropdown";
import { Grid} from "@mui/material";
import { MuiPickersUtilsProvider } from "@material-ui/pickers";
import { DatePicker } from "@material-ui/pickers";

import MomentUtils from "@date-io/moment";
import "moment/locale/tr";

const SearchComponent = (props) => {
    return (
        <div style={{ display: "flex" }}>
            {!props.inputDestroyer && (
                <Grid>
                    <Dropdown
                        label={props.transferStatusLabel}
                        options={props.transferStatus}
                        key={props.transferStatus.key}
                        value={props.transferStatus.value}
                        onChange={(e) =>
                            props.setTransferStatus(e.target.value)
                        }
                    />
                    <Dropdown
                        label={props.companyOfLabel}
                        options={props.companyOf}
                        key={props.companyOf.id}
                        value={props.companyOf.company_id}
                        onChange={(e) => props.setCompanyOf(e.target.value)}
                    />
                    <Dropdown
                        label={props.typeOfLabel}
                        options={props.typeOf}
                        key={props.typeOf.key}
                        value={props.typeOf.value}
                        onChange={(e) => props.setTypeOf(e.target.value)}
                    />
                    {/* <DesktopDatePicker
                        label="Date desktop"
                        inputFormat="dd/mm/yyyy"
                        value={value}
                        onChange={handleChange}
                        renderInput={(params) => <TextField {...params} />}
                    /> */}
                    <MuiPickersUtilsProvider utils={MomentUtils} locale={"tr"}>
                        <DatePicker
                            label={"Başlangıç Tarihi"}
                            placeholder="Başlangıç Tarihi"
                            value={props.beginDate ? props.beginDate : null}
                            onChange={(e) => props.handleBeginDateChange(e)}
                            format="DD/MM/YYYY"
                            // emptyLabel
                            //  InputLabelProps={{ shrink: true }}
                            // minDate={minDate}
                            // maxDate={maxDate}
                            // clearable
                        />
                        <br />
                        <br />
                        <DatePicker
                            label={"Bitiş Tarihi"}
                            placeholder="Bitiş Tarihi"
                            value={props.endDate ? props.endDate : null}
                            onChange={(e) => props.handleEndDateChange(e)}
                            format="DD/MM/YYYY"
                            // emptyLabel
                            //  InputLabelProps={{ shrink: true }}
                            // minDate={minDate}
                            // maxDate={maxDate}
                            // clearable
                        />
                    </MuiPickersUtilsProvider>
                </Grid>
            )}
        </div>
    );
};
export default SearchComponent;
