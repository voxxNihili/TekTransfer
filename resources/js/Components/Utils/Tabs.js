import React, { useEffect, useState } from "react";
import { inject, observer } from "mobx-react";
import PropTypes from "prop-types";
import { createTheme, makeStyles } from "@material-ui/core/styles";
import TabContentPanel from "./TabsPanel";
import Tabs from "@mui/material/Tabs";
import Tab from "@mui/material/Tab";
import Typography from "@mui/material/Typography";
import Box from "@mui/material/Box";
import axios from "axios";
import ProductCard from "./ProductCard";

// Data

//Components
// const theme = createTheme({
//     overrides: {
//       MuiTab: {
//         root: {
//           "&.MuiTab-root": {
//             backgroundColor: "black",
//             border: 0,
//             borderBottom: "2px solid",
//             "&:hover": {
//               border: 0,
//               borderBottom: "2px solid",
//             },
//           },
//           "&.Mui-selected": {
//             backgroundColor: "none",
//             borderBottom: "2px solid #373985",
//             borderColor: "#373985",
//           }
//         }
//       }
//     }
//   });
  
  // const useStyles = makeStyles((theme) => ({
  //   root: {
  //     width: "100%",
  //     flexGrow: 1,
  //     color: "#3739B5",
  //     backgroundColor: "white",
  //   },
  //   viewButtons: {
  //     marginTop: theme.spacing(2),
  //     marginBottom: theme.spacing(1),
  //   },
  // }));
  
  
const TabsComponent = (props) => {
    const initialTabIndex = 0;
    const [value, setValue] = useState(initialTabIndex);
    const [products, setProducts] = useState([]);
    const [categories, setCategories] = useState([]);
    const [refresh, setRefresh] = useState(false);

    const handleChange = (event, newValue) => {
        setValue(newValue);
    };
    useEffect(() => {
        axios
            .get(`/api/web/categoryToProduct`, {})
            .then((res) => {
                console.log("res", res);
                setProducts(res.data.data);
            })
            .catch((e) => console.log(e));

        // axios
        //     .get(`/api/web/category`, {

        //     })
        //     .then((res) => {
        //         console.log("res", res);
        //         setCategories(res.data.data);
        //     })
        //     .catch((e) => console.log(e));

        console.log(1, categories);
    }, [refresh]);

    return (
        <Box sx={{ my: "3rem"}}>
            <Tabs
          
                value={value}
                onChange={handleChange}
                aria-label="ürün kategorileri"
                centered
                // classes={classes}
                //  classes={{ indicator: classes.indicator }}
            >
                {products?.map((item, idx) => (
                    <Tab
                        label={item.name}
                        id={`kategori-${idx}`}
                        ariaControls={`ürün kategori-${idx}`}
                    />
                ))}
            </Tabs>
            {products.length > 0 &&
                products?.map((item, idx) => (
                    <TabContentPanel value={value} index={idx}>
                      <div className="row">
                      
                        {item.category_to_product.length > 0 &&
                            item.category_to_product?.map((item2, idx2) => (
                              <div className="col-4">
                                <ProductCard
                                    cardName={item.name + " " + item2.name}
                                />
                                </div>
                            ))}
                            </div>
                    </TabContentPanel>
                ))}
        </Box>
    );
};

export default inject("AuthStore")(observer(TabsComponent));
