import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { useRouter } from "../../Components/Hooks/useRouter";
import useIsomorphicLayoutEffect from "../../Components/Hooks/useIsomorphicLayoutEffect";
import {
    List,
    ListItem,
    Typography,
    TextField,
    Button,
} from "@material-ui/core";
import Layout from "../../Components/Layout/homeLayout";
import { Controller, useForm } from "react-hook-form";
import axios from "axios";
import { inject, observer } from "mobx-react";
import useStyles from "../../Components/style/theme";
import { useSnackbar } from "notistack";

const Register = (props) => {

    const [registeredSuccess, setRegisteredSuccess] = useState(false);
    const { enqueueSnackbar, closeSnackbar } = useSnackbar();
    const classes = useStyles();
    const router = useRouter();
    const { redirect } = router.query;
    const {
        handleSubmit,
        control,
        formState: { errors },
    } = useForm();

    useEffect(() => {
        if (props.AuthStore.appState != null) {
            if (props.AuthStore.appState.isLoggedIn) {
                return props.history.push("/");
            }
        }
    }, []);

    const delay = 3;

    useIsomorphicLayoutEffect(() => {
        if (!registeredSuccess) {
            console.log("1f",registeredSuccess)
            return;
          }
        let timer1 = setTimeout(() => props.history.push("/"), delay * 1000);
        return () => {   
            console.log("b",registeredSuccess);
            clearTimeout(timer1);
        };
     
    }, [registeredSuccess]);
   

    const submitHandler = (values) => {
        closeSnackbar();
        console.log("a",registeredSuccess);
        axios
            .post(`/api/auth/forgetPassword`, { ...values })
            .then((res) => {
                if (res.data.success) {
                    const userData = {
                        id: res.data.id,
                        name: res.data.name,
                        email: res.data.email,
                        access_token: res.data.access_token,
                    };
                    const appState = {
                        isLoggedIn: true,
                        user: userData,
                    };
                    props.AuthStore.saveToken(appState);
                    setRegisteredSuccess(true);
                    // location.reload();
                    // delayedRouter();
                
                } else {
                    alert("Giriş Yapamadınız");
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
        <Layout title="ForgetPassport">
            {!registeredSuccess ? (
                <div className="login-forget-container">
                    <form
                        onSubmit={handleSubmit(submitHandler)}
                        className={classes.form}
                        autoComplete="off"
                    >
                        <Typography component="h3" variant="h3">
                            Şifremi Unuttum
                        </Typography>
                        <List>
                            <ListItem>
                                <Controller
                                    name="email"
                                    control={control}
                                    defaultValue=""
                                    rules={{
                                        required: true,
                                        pattern:
                                            /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/,
                                    }}
                                    render={({ field }) => (
                                        <TextField
                                            variant="outlined"
                                            fullWidth
                                            id="email"
                                            label="Email"
                                            inputProps={{ type: "email" }}
                                            error={Boolean(errors.email)}
                                            helperText={
                                                errors.email
                                                    ? errors.email.type ===
                                                      "pattern"
                                                        ? "Email geçerli değil"
                                                        : "Email alanı boş bırakılamaz"
                                                    : ""
                                            }
                                            {...field}
                                        ></TextField>
                                    )}
                                ></Controller>
                            </ListItem>
                            <ListItem>
                                <Button
                                    variant="contained"
                                    type="submit"
                                    fullWidth
                                    color="primary"
                                >
                                    Şifre Talebi
                                </Button>
                            </ListItem>
                            <ListItem>
                                Zaten hesabınız var mı? &nbsp;
                                <Link to={`/login?redirect=${redirect || "/"}`}>
                                    Giriş Yap
                                </Link>
                            </ListItem>
                        </List>
                    </form>
                    {/* <Link className="mt-3" style={{display:'block'}} to="/login">Giriş</Link> */}
                    {/* <p className="mt-5 mb-3 text-muted">© 2022</p> */}
                    {/* </form> */}
                </div>
            ) : (
                <Typography component="h3" variant="h3">
                    Kayıt Tamamlandı, <br /> Yönlendiriliyorsunuz.
                </Typography>
            )}
        </Layout>
    );
};
export default inject("AuthStore")(observer(Register));
