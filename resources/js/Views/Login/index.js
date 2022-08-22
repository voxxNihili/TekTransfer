import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import Layout from '../../Components/Layout/homeLayout';
import { useRouter }  from "../../Components/Hooks/useRouter"
import {
    List,
    ListItem,
    Typography,
    TextField,
    Button,
  } from '@material-ui/core';
import { Controller, useForm } from 'react-hook-form';

import axios from "axios";
import { inject, observer } from "mobx-react";
import useStyles from "../../Components/style/theme";


const Login = (props) => {
    // const [errors, setErrors] = useState([]);
    const [error, setError] = useState("");
    const {
        handleSubmit,
        control,
        formState: { errors },
      } = useForm();
    const classes = useStyles();
    const router = useRouter();
    const { redirect } = router.query; 
    useEffect(() => {
        if (props.AuthStore.appState != null) {
            if (props.AuthStore.appState.isLoggedIn) {
                return props.history.push("/");
            }
        }
    });

    const submitHandler = (values) => {
        axios
            .post(`/api/auth/login`, { ...values })
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
                    //props.history.push('/');
                    window.location.reload();
                } else {
                    alert("Giriş Yapamadınız");
                }
            })
            .catch((error) => {
                if (error.response) {
                    let err = error.response.data;
                    if (err.errors) {
                        setErrors(err.errors);
                    } else {
                        setError(error.response.data.message);
                    }
                    //alert(err.errors)
                } else if (error.request) {
                    let err = error.request;
                    setError(err);
                } else {
                    setError(error.message);
                }
            });
    };
    // let arr = [];
    // if (errors.length > 0) {
    //     Object.values(errors).forEach((value) => {
    //         arr.push(value);
    //     });
    // }
    return (
        <Layout title="Login">
        <form onSubmit={handleSubmit(submitHandler)} className={classes.form}>
          <Typography component="h1" variant="h1">
            Giriş Yap
          </Typography>
          <List>
            <ListItem>
              <Controller
                name="email"
                control={control}
                defaultValue=""
                rules={{
                  required: true,
                  pattern: /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/,
                }}
                render={({ field }) => (
                  <TextField
                    variant="outlined"
                    fullWidth
                    id="email"
                    label="Email"
                    inputProps={{ type: 'email' }}
                    error={Boolean(errors.email)}
                    helperText={
                      errors.email
                        ? errors.email.type === 'pattern'
                          ? 'Email Formatı Hatalı'
                          : 'Email Zorunludur'
                        : ''
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
                    inputProps={{ type: 'password' }}
                    error={Boolean(errors.password)}
                    helperText={
                      errors.password
                        ? errors.password.type === 'minLength'
                          ? 'Şifre Uzunluğu 6 ya da daha fazla olmalı'
                          : 'Şifre Zorunludur'
                        : ''
                    }
                    {...field}
                  ></TextField>
                )}
              ></Controller>
            </ListItem>
            <ListItem>
              <Button variant="contained" type="submit" fullWidth color="primary">
                Giriş Yap
              </Button>
            </ListItem>
            <ListItem>
              Hesabın yok mu? &nbsp;
              <Link to={`/register?redirect=${redirect || '/'}`} >
                Kayıt ol
              </Link>
            </ListItem>
          </List>
        </form>
        </Layout>
    );
};
export default inject("AuthStore")(observer(Login));
