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
    IconButton,
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
import { AccountCircle } from "@mui/icons-material";

const HomeLayout = (props) => {
    const classes = useStyles();
    const [anchorEl, setAnchorEl] = useState(null);
    const [user, setUser] = useState({});
    const [userRole, setUserRole] = useState({});
    // const [register,setRegister] = useState(false);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    props.AuthStore.getToken();
    const history = useHistory();
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
                main: "#234E70",
            },
            secondary: {
                main: "#FFFFFF",
            },
            ternary: {
                main: "#A865C9",
            },
        },
    });

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
                setUserRole(res.data.role);

                setIsLoggedIn(res.data.isLoggedIn);
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
            .then((res) => {
                console.log(res);
                history.go(0);
            })
            .catch((e) => console.log(e));
        props.AuthStore.removeToken();

        // history.push("/");
    };

    const loginClickHandler = (e) => {
        {
            !isLoggedIn && history.push("/login");
        }
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
                    <Link to="/">
                        <Typography className={classes.brand}>
                            MuhTek
                        </Typography>
                    </Link>
                    <Container  className={classes.navbarLinks}>
                        <div className={classes.grow005}></div>
                        {userRole === "superAdmin" && (
                            <Link to="/admin">
                                <Typography className={classes.brand}>
                                    Yönetim Paneli
                                </Typography>
                            </Link>
                        )}
                        {userRole === "superAdmin" && (
                            <Link to="/musteriler">
                                <Typography className={classes.brand}>
                                    Müşteri & Tedarikçi
                                </Typography>
                            </Link>
                        )}
                        <Link to="/kategoriler">
                            <Typography className={classes.brand}>
                                Kategoriler
                            </Typography>
                        </Link>
                        <Link to="/urunler">
                            <Typography className={classes.brand}>
                                Ürünler
                            </Typography>
                        </Link>
                        <Link to="/sorgu-parametreleri">
                            <Typography className={classes.brand}>
                                Sorgu Parametreleri
                            </Typography>
                        </Link>
                        <Link to="/sorgular">
                            <Typography className={classes.brand}>
                                Sorgular
                            </Typography>
                        </Link>
                        <Link to="/raporlar">
                            <Typography className={classes.brand}>
                                Raporlar
                            </Typography>
                        </Link>
                        {userRole === "superAdmin" && (
                            <Link to="/siparisler">
                                <Typography className={classes.brand}>
                                    Siparişler
                                </Typography>
                            </Link>
                        )}
                        
                    </Container>
                    {/* <div className={classes.grow1}></div> */}
                    <div>
                        {isLoggedIn ? (
                            <>
                                <Button
                                    aria-controls="account-menu"
                                    aria-haspopup="true"
                                    onClick={loginClickHandler}
                                    className={classes.mainButton}
                                >
                                    {user.name}
                                    {/* <AccountCircle /> */}
                                </Button>
                                <Menu
                                    id="account-menu"
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
                            <Button
                                aria-controls="account-menu"
                                aria-haspopup="true"
                                onClick={loginClickHandler}
                                className={classes.mainButton}
                                color="ternary"
                            >
                                Giriş Yap
                            </Button>

                            // <Button
                            //     aria-controls="simple-menu"
                            //     aria-haspopup="true"
                            //     onClick={()=>{history.push("/login")}}
                            //     className={classes.mainButton}
                            // >
                            //     Giriş Yap
                            // </Button>
                            // <Link href="/login">Giriş Yap</Link>
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
