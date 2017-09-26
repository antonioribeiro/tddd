// ------------------------------------------------
// ---- FileUrl

var FileUrl = React.createClass({
    render: function() {
        return (
            <a
                {...this.props}
                href="javascript:;"
                onClick={this.openFile}
            />
        );
    },

    openFile: function() {
        console.log('open file');
    },
});
