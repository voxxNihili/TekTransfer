import React, { useState, useEffect } from "react";
import axios from "axios";
import { inject, observer } from "mobx-react";
import { useHistory, Link } from "react-router-dom";
import {
    AppBar,
    Toolbar,
    Typography,
    // Container,
    createTheme,
    ThemeProvider,
    CssBaseline,
    Switch,
    Badge,
    // Button,
    Menu,
    MenuItem,
} from "@material-ui/core";
import useStyles from "../style/theme";
import {
    Navbar,
    Nav,
    NavDropdown,
    Container,
    Button,
    Form,
} from "react-bootstrap";
import { LinkContainer } from "react-router-bootstrap";
const HomeLayout = (props) => {
    const [user, setUser] = useState({});
    // const [register,setRegister] = useState(false);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    props.AuthStore.getToken();
    const history = useHistory();

    useEffect(() => {
        const token =
            props.AuthStore.appState != null
                ? props.AuthStore.appState.user.access_token
                : null;
        axios
            .post(
                `/api/authenticate`,
                {},
                {
                    headers: {
                        Authorization: "Bearer " + token,
                    },
                }
            )
            .then((res) => {
                if (!res.data.isLoggedIn) {
                    setIsLoggedIn(false);
                }
                setUser(res.data.user);
                setIsLoggedIn(res.data.isLoggedIn);
                // console.log("res", res);
            })
            .catch((e) => {
                setIsLoggedIn(false);
            });
    }, []);

    const logoutClickHandler = () => {
        setAnchorEl(null);
        axios
            .post(
                `/api/logout`,
                {},
                {
                    headers: {
                        Authorization:
                            "Bearer " +
                            props.AuthStore.appState.user.access_token,
                    },
                }
            )
            .then((res) => console.log(res))
            .catch((e) => console.log(e));
        props.AuthStore.removeToken();

        history.push("/login");
    };
    const theme = createTheme({
        typography: {
            fontFamily: "Arvo",
            h1: {
                fontSize: "1.2rem",
                fontWeight: 400,
                margin: "1rem 0",
            },
            h2: {
                fontSize: "1.4rem",
                fontWeight: "400",
                margin: "1rem 0",
            },
        },
        palette: {
            //   type: darkMode ? "dark" : "light",
            primary: {
                main: "#008dd2",
            },
            secondary: {
                main: "#FFFFFF",
            },
            ternary: {
                main: "#66c3d0",
            },
        },
    });
    const classes = useStyles();
    const [anchorEl, setAnchorEl] = useState(null);
    const loginClickHandler = (e) => {
        setAnchorEl(e.currentTarget);
    };
    const loginMenuCloseHandler = () => {
        setAnchorEl(null);
    };

    return (
        <ThemeProvider theme={theme}>
            <CssBaseline />
            <AppBar position="static" className={classes.navbar}>
                <Toolbar>
                <div className={classes.grow01}></div>

                    <Link href="/">
                        <Typography className={classes.brand}>
                            MuhTek
                        </Typography>
                    </Link>
                    <div className={classes.grow005}></div>

                    <Link href="/kategoriler">
                        <Typography className={classes.brand}>
                           Kategoriler
                        </Typography>
                    </Link>
                    <Link href="/urunler">
                        <Typography className={classes.brand}>
                           Ürünler
                        </Typography>
                    </Link>
                    <Link href="/siparisler">
                        <Typography className={classes.brand}>
                           Siparişler
                        </Typography>
                    </Link>
                    <div className={classes.grow1}></div>
                    <div>
                        {isLoggedIn ? (
                            <>
                                <Button
                                    aria-controls="simple-menu"
                                    aria-haspopup="true"
                                    onClick={loginClickHandler}
                                    className={classes.navbarButton}
                                >
                                    {user.name}
                                </Button>
                                <Menu
                                    id="simple-menu"
                                    anchorEl={anchorEl}
                                    keepMounted
                                    open={Boolean(anchorEl)}
                                    onClose={loginMenuCloseHandler}
                                >
                                    <MenuItem onClick={loginMenuCloseHandler}>
                                        Profil Düzenle
                                    </MenuItem>
                                    <MenuItem onClick={logoutClickHandler}>
                                        Çıkış
                                    </MenuItem>
                                </Menu>
                            </>
                        ) : (
                            <Link href="/login">Login</Link>
                        )}
                    </div>
                <div className={classes.grow01}></div>

                </Toolbar>
            </AppBar>
            <Container className={classes.main}>{props.children}</Container>
            {/* <footer className={classes.footer}>
              <Typography>All Rights Reserved. Next E-Commerce</Typography>
            </footer> */}
        </ThemeProvider>
    );
};

export default inject("AuthStore")(observer(HomeLayout));
