import React, { useState, useEffect } from "react";
import AppBar from "@mui/material/AppBar";
import Box from "@mui/material/Box";
import CssBaseline from "@mui/material/CssBaseline";
import Divider from "@mui/material/Divider";
import Drawer from "@mui/material/Drawer";
import IconButton from "@mui/material/IconButton";
import InboxIcon from "@mui/icons-material/MoveToInbox";
import List from "@mui/material/List";
import ListItem from "@mui/material/ListItem";
import ListItemButton from "@mui/material/ListItemButton";
import ListItemIcon from "@mui/material/ListItemIcon";
import ListItemText from "@mui/material/ListItemText";
import MailIcon from "@mui/icons-material/Mail";
import MenuIcon from "@mui/icons-material/Menu";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import axios from "axios";
import { inject, observer } from "mobx-react";
import { useHistory, Link } from "react-router-dom";
import Footer from "./Footer";
import {
    // Container,
    createTheme,
    ThemeProvider,
    Switch,
    Badge,
    // Button,
    Menu,
    MenuItem,
    Grid,
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
import Lovely from "../../Components/src/LovelyBanner/Lovely";
import AdminPanelSettingsIcon from "@mui/icons-material/AdminPanelSettings";
import ManageAccountsIcon from "@mui/icons-material/ManageAccounts";
import CategoryIcon from "@mui/icons-material/Category";
import GroupWorkIcon from "@mui/icons-material/GroupWork";
import QueryStatsIcon from "@mui/icons-material/QueryStats";
import PsychologyAltIcon from "@mui/icons-material/PsychologyAlt";
import AssessmentIcon from "@mui/icons-material/Assessment";
import ShoppingCartIcon from "@mui/icons-material/ShoppingCart";
import ArticleIcon from "@mui/icons-material/Article";
import CreditCardIcon from "@mui/icons-material/CreditCard";
import MonetizationOnIcon from "@mui/icons-material/MonetizationOn";
import FileUploadIcon from "@mui/icons-material/FileUpload";
const drawerWidth = 232;

const HomeLayout = (props) => {
    const { windowProp } = props;
    const [mobileOpen, setMobileOpen] = useState(false);
    const [pageTitle, setPageTitle] = useState(false);
    const classes = useStyles();
    const [anchorEl, setAnchorEl] = useState(null);
    const [user, setUser] = useState({});
    const [userRole, setUserRole] = useState({});
    const handleDrawerToggle = () => {
        setMobileOpen(!mobileOpen);
    };
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    props.AuthStore.getToken();
    const history = useHistory();
    const container =
        windowProp !== undefined ? () => window().document.body : undefined;
    const theme = createTheme({
        typography: {
            fontFamily: "Arvo",
            fontWeight: 600,

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

    const drawer = (
        <Box className={classes.sidebar}>
            <Link
                to="/"
                className={
                    window.location.pathname.split("/")[1] === "admin" &&
                    classes.currentPath
                }
            >
                <Box
                    component="img"
                    sx={{
                        height: 64,
                        width: "100%",
                    }}
                    alt="Your logo."
                    src="\images\tlogo.png"
                />
            </Link>
            <Divider />
            <List
                className={classes.sidebarLinks}
                sx={{
                    "& a:hover": {
                        "& .adminPanelIcon, & .manageAccIcon, & .categoryIcon,, & .groupWorkIcon, & .queryStatsIcon, & .psychologyAltIcon, & .assessmentIcon, & .shoppingCartIcon, & .articleIcon, & .creditCardIcon, & .monetizationOnIcon, & .fileUploadIcon":
                            {
                                color: theme.palette.primary.main,
                                transition: theme.transitions,
                            },
                    },
                }}
            >
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/admin"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "admin" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <AdminPanelSettingsIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "admin"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="adminPanelIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Yönetim Paneli
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/musteriler"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "musteriler" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <ManageAccountsIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "musteriler"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="manageAccIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Müşteri & Tedarikçi
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        {/* <Divider />
                <Link to="/kategoriler">
                    <ListItem sx={{ p: 0.5 }}>
                        <ListItemButton>
                            <ListItemIcon>
                                <CategoryIcon
                                    sx={{ color: "#FFFFFF" }}
                                    className="categoryIcon"
                                />
                            </ListItemIcon>

                            <Typography className={classes.brand}>
                                Kategoriler
                            </Typography>
                        </ListItemButton>
                    </ListItem>
                </Link> */}
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/urunler"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "urunler" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <GroupWorkIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "urunler"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="groupWorkIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Ürünler
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        {" "}
                        <Link
                            to="/sorgu-parametreleri"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "sorgu-parametreleri" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <QueryStatsIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] ===
                                                    "sorgu-parametreleri"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="queryStatsIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Sorgu Parametreleri
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/sorgular"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "sorgular" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <PsychologyAltIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "sorgular"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="psychologyAltIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Sorgular
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/raporlar"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "raporlar" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <AssessmentIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "raporlar"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="assessmentIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Raporlar
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                {userRole === "superAdmin" && (
                    <>
                        <Link
                            to="/siparisler"
                            className={
                                window.location.pathname.split("/")[1] ===
                                    "siparisler" && classes.currentPath
                            }
                        >
                            <ListItem sx={{ p: 0.5 }}>
                                <ListItemButton>
                                    <ListItemIcon>
                                        <ShoppingCartIcon
                                            sx={{
                                                color:
                                                    window.location.pathname.split(
                                                        "/"
                                                    )[1] === "siparisler"
                                                        ? theme.palette.primary
                                                              .main
                                                        : "#FFFFFF",
                                            }}
                                            className="shoppingCartIcon"
                                        />
                                    </ListItemIcon>

                                    <Typography className={classes.brand}>
                                        Siparişler
                                    </Typography>
                                </ListItemButton>
                            </ListItem>
                        </Link>
                        <Divider />
                    </>
                )}
                <Link
                    to="/faturalar"
                    className={
                        window.location.pathname.split("/")[1] ===
                            "faturalar" && classes.currentPath
                    }
                >
                    <ListItem sx={{ p: 0.5 }}>
                        <ListItemButton>
                            <ListItemIcon>
                                <ArticleIcon
                                    sx={{
                                        color:
                                            window.location.pathname.split(
                                                "/"
                                            )[1] === "faturalar"
                                                ? theme.palette.primary.main
                                                : "#FFFFFF",
                                    }}
                                    className="articleIcon"
                                />
                            </ListItemIcon>

                            <Typography className={classes.brand}>
                                Logo Fatura Aktarımları
                            </Typography>
                        </ListItemButton>
                    </ListItem>
                </Link>
                <Link
                    to="/logo-kredi-karti-aktarimlari"
                    className={
                        window.location.pathname.split("/")[1] ===
                            "logo-kredi-karti-aktarimlari" &&
                        classes.currentPath
                    }
                >
                    <ListItem sx={{ p: 0.5 }}>
                        <ListItemButton>
                            <ListItemIcon>
                                <CreditCardIcon
                                    sx={{
                                        color:
                                            window.location.pathname.split(
                                                "/"
                                            )[1] ===
                                            "logo-kredi-karti-aktarimlari"
                                                ? theme.palette.primary.main
                                                : "#FFFFFF",
                                    }}
                                    className="creditCardIcon"
                                />
                            </ListItemIcon>

                            <Typography className={classes.brand}>
                                Logo Kredi Kartı Aktarımları
                            </Typography>
                        </ListItemButton>
                    </ListItem>
                </Link>
                {userRole === "superAdmin" && (
                    <Link
                        to="/logo-nakit-odeme-aktarimlari"
                        className={
                            window.location.pathname.split("/")[1] ===
                                "logo-nakit-odeme-aktarimlari" &&
                            classes.currentPath
                        }
                    >
                        <ListItem sx={{ p: 0.5 }}>
                            <ListItemButton>
                                <ListItemIcon>
                                    <MonetizationOnIcon
                                        sx={{
                                            color:
                                                window.location.pathname.split(
                                                    "/"
                                                )[1] ===
                                                "logo-nakit-odeme-aktarimlari"
                                                    ? theme.palette.primary.main
                                                    : "#FFFFFF",
                                        }}
                                        className="monetizationOnIcon"
                                    />
                                </ListItemIcon>

                                <Typography className={classes.brand}>
                                    Logo Nakit Aktarımları
                                </Typography>
                            </ListItemButton>
                        </ListItem>
                    </Link>
                )}
                <Link
                    to="/file-upload"
                    className={
                        window.location.pathname.split("/")[1] ===
                            "file-upload" && classes.currentPath
                    }
                >
                    <ListItem sx={{ p: 0.5 }}>
                        <ListItemButton>
                            <ListItemIcon>
                                <FileUploadIcon
                                    sx={{
                                        color:
                                            window.location.pathname.split(
                                                "/"
                                            )[1] === "file-upload"
                                                ? theme.palette.primary.main
                                                : "#FFFFFF",
                                    }}
                                    className="fileUploadIcon"
                                />
                            </ListItemIcon>

                            <Typography className={classes.brand}>
                                Dosya Yükleme
                            </Typography>
                        </ListItemButton>
                    </ListItem>
                </Link>
            </List>
        </Box>
    );

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
                // console.log(res);
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
    const serviceMenu = [
        {
            name: "Yönetim Paneli",
            url: "/admin",
            permission: "admin",
        },
        {
            name: "Kullanıcılar",
            url: "/musteriler",
            permission: "admin",
        },
        {
            name: "Ürünler",
            url: "/urunler",
            permission: "admin",
        },
        {
            name: "Sorgu Parametreleri",
            url: "/sorgu-parametreleri",
            permission: "admin",
        },
        {
            name: "Sorgular",
            url: "/sorgular",
            permission: "admin",
        },
        {
            name: "Raporlar",
            url: "/raporlar",
            permission: "admin",
        },
        {
            name: "Siparişler",
            url: "/siparisler",
            permission: "admin",
        },
        {
            name: "Logo Fatura Aktarımları",
            url: "/faturalar",
            permission: "admin",
        },
        {
            name: "Logo Kredi Kartı Aktarımları",
            url: "/logo-kredi-karti-aktarimlari",
            permission: "admin",
        },
        {
            name: "Logo Nakit Ödeme Aktarımları",
            url: "/logo-nakit-odeme-aktarimlari",
            permission: "admin",
        },
        {
            name: "Excel Fatura Aktarımı",
            url: "/file-upload",
            permission: "uploader",
        },
    ];
    useEffect(() => {
        const pageTitle = serviceMenu.find(
            (item) => item.url == window.location.pathname
        );
        console.log("pagetitle", pageTitle);
        setPageTitle(pageTitle);
    }, []);

    return (
        <ThemeProvider theme={theme}>
            <AppBar
                position="fixed"
                sx={{
                    width: { sm: `calc(100% - ${drawerWidth}px)` },
                    ml: { sm: `${drawerWidth}px` },
                    backgroundColor: "transparent",
                    boxShadow: "unset",
                }}
            >
                <Toolbar>
                    <IconButton
                        color="primary"
                        aria-label="open drawer"
                        edge="start"
                        onClick={handleDrawerToggle}
                        sx={{ mr: 2, display: { sm: "none" } }}
                    >
                        <MenuIcon />
                    </IconButton>
                    {isLoggedIn ? (
                        <>
                            <Typography className={classes.breadCrumbs}>
                                {pageTitle?.name}
                            </Typography>
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
                            color={theme.palette.primary.main}
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
                </Toolbar>
            </AppBar>
            <Box sx={{ display: "flex" }}>
                <CssBaseline />

                <Lovely />
                {(userRole === "superAdmin" || userRole === "accounting") && (
                    <Box
                        component="nav"
                        sx={{
                            width: { sm: drawerWidth },
                            flexShrink: { sm: 0 },
                        }}
                        aria-label="mailbox folders"
                    >
                        <Drawer
                            container={container}
                            variant="temporary"
                            open={mobileOpen}
                            onClose={handleDrawerToggle}
                            ModalProps={{
                                keepMounted: true,
                            }}
                            sx={{
                                display: { xs: "block", sm: "none" },
                                "& .MuiDrawer-paper": {
                                    width: drawerWidth,
                                    backgroundColor: theme.palette.primary.main,
                                },
                            }}
                        >
                            {drawer}
                        </Drawer>
                        <Drawer
                            variant="permanent"
                            sx={{
                                display: { xs: "none", sm: "block" },
                                "& .MuiDrawer-paper": {
                                    // top: "64px",
                                    width: drawerWidth,
                                    backgroundColor: theme.palette.primary.main,
                                },
                            }}
                            open
                        >
                            {drawer}
                        </Drawer>
                    </Box>
                )}
                <Box
                    component="main"
                    sx={{
                        flexGrow: 1,
                        p: 3,
                        width: { sm: `calc(100% - ${drawerWidth}px)` },
                        minHeight: { xs: `calc(100vh - 64px)` },
                    }}
                >
                    <Toolbar />
                    {props.children}
                </Box>
            </Box>
            <Box className={classes.footer}>
                <Footer />
            </Box>
        </ThemeProvider>
    );
};

export default inject("AuthStore")(observer(HomeLayout));
