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
    // const [error, setError] = useState("");
    const [error, setError] = useState("");
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
            console.log("1f", registeredSuccess);
            return;
        }
        let timer1 = setTimeout(() => props.history.push("/"), delay * 1000);
        return () => {
            console.log("b", registeredSuccess);
            clearTimeout(timer1);
        };
    }, [registeredSuccess]);
    // const delayedRouter = () => {
    //     useIsomorphicLayoutEffect(() => {
    //         let timer1 = setTimeout(
    //             () => setHasTimeElapsed(true),
    //             delay * 1000
    //         );
    //         return () => {
    //             clearTimeout(timer1);
    //         };
    //     }, []);
    // };
    // const timer = setTimeout(() => {
    //     setRegisteredSuccess(true);
    // }, 2000);
    // return () => {
    //     clearTimeout(timer);
    //     setRegisteredSuccess(false);
    //
    // };

    const submitHandler = (values) => {
        closeSnackbar();
        if (values.password !== values.password_confirmation) {
            enqueueSnackbar("Passwords don't match", { variant: "error" });
            return;
        }

        console.log("a", registeredSuccess);
        axios
            .post(`/api/auth/register`, { ...values })
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
                }
            })
            .catch((err) => {
                // console.log("errrrrrrrrr", message);
                // console.log("errrrrrr", err.message);
                // console.log("errrrrrr", data.message);
                // console.log("errrrrrr", err.data.message);
                // console.log("errrrrrr", err.errors?.email[0]);
                // swal(res.data.message);
                enqueueSnackbar(err.message==="Request failed with status code 422" ? "Bu E-mail kullanılmaktadır." : err.message, { variant: "error" });
                setError(err.res.data.message);
            });
    };
    // let arr = [];
    // Object.values(errors).forEach((value) => {
    //     arr.push(value);
    // });
    return (
        <Layout title="Register">
            {console.log("yavşş",error)}
            {!registeredSuccess ? (
                <div className="login-register-container">
                    {/* <form autoComplete="off" className="form-signin"> */}

                    {/* <h1 className="h3 mb-3 font-weight-normal">Hemen Kayıt Ol</h1> */}
                    {/* {arr.length != 0 && arr.map((item) => <p>{item}</p>)}
                    {error != "" && <p>{error}</p>} */}
                    <form
                        onSubmit={handleSubmit(submitHandler)}
                        className={classes.form}
                        autoComplete="off"
                    >
                        <Typography component="h3" variant="h3">
                            Hemen Kayıt Ol
                        </Typography>
                        <List>
                            <ListItem>
                                <Controller
                                    name="name"
                                    control={control}
                                    defaultValue=""
                                    rules={{
                                        required: true,
                                        minLength: 2,
                                    }}
                                    render={({ field }) => (
                                        <TextField
                                            variant="outlined"
                                            fullWidth
                                            id="name"
                                            label="İsim"
                                            inputProps={{ type: "name" }}
                                            error={Boolean(errors.name)}
                                            helperText={
                                                errors.name
                                                    ? errors.name.type ===
                                                      "minLength"
                                                        ? "İsim 1 harften az olamaz"
                                                        : "İsim alanı boş bırakılamaz"
                                                    : ""
                                            }
                                            {...field}
                                        ></TextField>
                                    )}
                                ></Controller>
                            </ListItem>
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
                                <Controller
                                    name="password"
                                    control={control}
                                    defaultValue=""
                                    rules={{
                                        required: true,
                                        minLength: 6,
                                    }}
                                    render={({ field }) => (
                                        <TextField
                                            variant="outlined"
                                            fullWidth
                                            id="password"
                                            label="Şifre"
                                            inputProps={{ type: "password" }}
                                            error={Boolean(errors.password)}
                                            helperText={
                                                errors.password
                                                    ? errors.password.type ===
                                                      "minLength"
                                                        ? "Şifre en az 6 haneli olmalı"
                                                        : "Şifre alanı boş bırakılamaz"
                                                    : ""
                                            }
                                            {...field}
                                        ></TextField>
                                    )}
                                ></Controller>
                            </ListItem>
                            <ListItem>
                                <Controller
                                    name="password_confirmation"
                                    control={control}
                                    defaultValue=""
                                    rules={{
                                        required: true,
                                        minLength: 6,
                                    }}
                                    render={({ field }) => (
                                        <TextField
                                            variant="outlined"
                                            fullWidth
                                            id="password_confirmation"
                                            label="Şifre (Tekrar)"
                                            inputProps={{ type: "password" }}
                                            error={Boolean(
                                                errors.password_confirmation
                                            )}
                                            helperText={
                                                errors.password_confirmation
                                                    ? errors
                                                          .password_confirmation
                                                          .type === "minLength"
                                                        ? "En az 6 haneli eşleşen şifreyi giriniz"
                                                        : "Lütfen eşleşen şifreyi giriniz"
                                                    : ""
                                            }
                                            {...field}
                                        ></TextField>
                                    )}
                                ></Controller>
                            </ListItem>
                            {console.log("err", error)}
                            {error && (
                                <p className={classes.loginError}>
                                    Email ya da Şifre Hatalı
                                </p>
                            )}
                            <ListItem>
                                <Button
                                    variant="contained"
                                    type="submit"
                                    fullWidth
                                    color="primary"
                                >
                                    Kayıt Ol
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
