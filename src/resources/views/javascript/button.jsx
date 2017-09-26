// ------------------------------------------------
// ---- Bootstrap Button

var BootstrapButton = React.createClass({
    render: function() {
        return (
            <a
                {...this.props}
                href="javascript:;"
                role="button"
                className={(this.props.className || '') + ' btn'}
                data-keyboard="true"
            />
        );
    }
});
