// ------------------------------------------------
// ---- State

var State = React.createClass({
    render: function()
    {
        var color;

        if (this.props.type == 'running')
        {
            color = 'info';
        }
        else if (this.props.type == 'ok')
        {
            color = 'success';
        }
        else if (this.props.type == 'failed')
        {
            color = 'danger';
        }
        else if (this.props.type == 'queued')
        {
            color = 'default';
        }

        return (
            <span className={"label label-"+color}>{this.props.type}</span>
        );
    }
});
