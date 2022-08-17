import React from 'react';
import PropTypes from 'prop-types';

// Material UI
import { Box } from '@material-ui/core';

function TabContentPanel(props) {
  const { children, value, index, ...other } = props;
  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`simple-tabpanel-${index}`}
      aria-labelledby={`simple-tab-${index}`}
      {...other}
    >
      {value === index && <Box >{children}</Box>}
    </div>
  );
}

TabContentPanel.propTypes = {
  children: PropTypes.node,
  index: PropTypes.any.isRequired,
  value: PropTypes.any.isRequired,
};

export default TabContentPanel;