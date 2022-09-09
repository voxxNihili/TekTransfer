const Dropdown = ({ label, value, options, onChange }) => {
    return (
        <div className="form-group">
            <label>
                {label} </label>
                <select
                    className="form-control"
                    value={value}
                    onChange={onChange}
                >
                    {options.map((option) => (
                        <option key={option.key} value={option.value}>{option.label}</option>
                    ))}
                    
                </select>
           
        </div>
    );
};
export default Dropdown;
