// ------------------------------------------------
// ---- Run Button

var RunButton = React.createClass({
    render: function()
    {
        if (this.props.test.enabled && this.props.test.state !== 'running' && this.props.test.state !== 'queued')
        {
            return (
                <div className="example">
                    <button
                        type="button"
                        className="btn btn-sm btn-warning"
                        onClick={this.runTest}
                        data-keyboard="true"
                    >
                        Run
                    </button>
                </div>
            );
        }

        return false;
    },

    runTest: function()
    {
        jQuery.ajax({
            url: '/ci-watcher/tests/run/'+this.props.test.id,

            dataType: 'json',

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

});
