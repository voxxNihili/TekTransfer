import { makeStyles } from "@material-ui/core";

const useStyles = makeStyles({
  navbar: {
    backgroundColor: "#234E70",
    "& a": {
      color: "#FBF8BE",
      marginLeft: 10,
    },
  },
  navbarButton: {
    color: "#FFFFFF",
    textTransform: "initial"
  },
  brand: {
    fontWeight: "bold",
    fontSize: "1.2rem",
  },
  grow005: {
    flexGrow: "0.05",
  },
  grow01: {
    flexGrow: "0.1",
  },
  grow1: {
    flexGrow: "1",
  },
  main: {
    minHeight: "80vh",
  },
  footer: {
    marginTop: 10,
    textAlign: "center",
  },
  section: {
    marginTop: 10,
    marginBottom: 10
  },
  bold: {
    fontWeight: 600
  },
  form: {
    maxWidth: 800,
    margin: "10px auto"
  },
  transparentBackground: {
    backgroundColor: "transparent"
  },
  error: {
    color: "#f04040"
  }
});
export default useStyles;
