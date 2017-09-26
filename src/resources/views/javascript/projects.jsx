// ------------------------------------------------
// ---- Projects

var ProjectsMenu = React.createClass({
    getInitialState: function()
    {
        return {data: [], selected: { name: '', id: null}};
    },

    loadFromServer: function()
    {
        jQuery.ajax({
            url: this.props.url,

            dataType: 'json',

            success: function(data)
            {
                this.setState({data: data.projects});
                EventSystem.fire('selected.changed', { selected: { name: data.projects[0].name, id: data.projects[0].id }});
            }.bind(this),

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    componentDidMount: function()
    {
        this.loadFromServer();
    },

    render: function()
    {
        return (
            <ProjectsMenuItems data={this.state.data} />
        );
    }
});

// ------------------------------------------------
// ---- Menu Items

var ProjectsMenuItems = React.createClass({
    getInitialState: function()
    {
        return {data: [], selected: { name: '', id: null}};
    },

    componentDidMount: function()
    {
        EventSystem.listen('selected.changed', this.selectedChanged);
    },

    selectedChanged: function(event)
    {
        this.setState({selected: event.selected});
    },

    handleClick: function(name, id)
    {
        EventSystem.fire('selected.changed', { selected: { name: name, id: id }});
    },

    render: function()
    {
        var nodes = [];

        for (index = 0; index < this.props.data.length; ++index)
        {
            project = this.props.data[index];

            nodes.push(
                <li
                    key={project.id}
                    className={project.id == this.state.selected.id ? 'list-group-item active' : 'list-group-item'}
                    onClick={this.handleClick.bind(this, project.name, project.id)}
                >
                    {project.name}
                </li>
            );
        }

        return (
            <div>
                {nodes}
            </div>
        );
    },
});
