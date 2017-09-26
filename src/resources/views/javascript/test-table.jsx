// ------------------------------------------------
// ---- Test Table

var TestsTable = React.createClass({
    getInitialState: function() {
        return {
            data: [],

            socket: {
                io: null,
                connected: false,
            },

            selected: {
                name: '',
                id: null
            }
        };
    },

    loadFromServer: function()
    {
        if (this.state.selected.id)
        {
            jQuery.ajax(
                {
                    url: this.props.url + this.state.selected.id,

                    dataType: 'json',

                    success: function(data) {
                        this.setState({data: data.tests});
                    }.bind(this),

                    error: function(xhr, status, err) {
                        console.error(this.props.url, status, err.toString());
                    }.bind(this)
                });
        }
    },

    componentDidMount: function()
    {
        this.loadFromServer();

        setInterval(this.loadFromServer, this.props.pollInterval);

        EventSystem.listen('selected.changed', this.selectedChanged);
    },

    selectedChanged: function(event)
    {
        this.setState({selected: event.selected});

        this.loadFromServer();
    },

    render: function()
    {
        return (
            <TestList data={this.state.data} />
        );
    }
});

// ------------------------------------------------
// ---- Test List

var TestList = React.createClass({
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

    runAll: function(event)
    {
        jQuery.ajax({
            url: '/ci-watcher/tests/run/all/'+this.state.selected.id,

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    resetState: function(event)
    {
        jQuery.ajax({
            url: '/ci-watcher/tests/reset/'+this.state.selected.id,

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    toogleAll: function(event, whatever)
    {
        jQuery.ajax({
            url: '/ci-watcher/tests/enable/'+event.target.checked+'/'+this.state.selected.id,

            dataType: 'json',

            success: function(data)
            {
                jQuery('.testCheckbox').prop('checked', data.state);
            }.bind([this, event]),

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    render: function()
    {
        var testNodes = this.props.data.map(function (test)
        {
            return (
                <TestRow key={test.id} test={test} projectId={this.state.selected.id} />
            );
        }, this);

        return (
            <div>
                <div className="row table-header">
                    <div className="col-md-9">
                        <h2>{this.state.selected.name}</h2>
                    </div>

                    <div className="col-md-3 text-right">
                        <BootstrapButton
                            onClick={this.runAll}
                            className="btn-danger"
                            dataKeyboard="true"
                        >
                            run all
                        </BootstrapButton>
                        &nbsp;
                        <BootstrapButton
                            onClick={this.resetState}
                            className="btn-warning"
                            dataKeyboard="true"
                        >
                            reset state
                        </BootstrapButton>
                    </div>
                </div>

                <div className="row">
                    <div className="col-md-12">
                        <table className="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input
                                            type="checkbox"
                                            title="Mark to enable test"
                                            onClick={this.toogleAll}
                                        />
                                    </th>
                                    <th>run</th>
                                    <th width="70%">test</th>
                                    <th>last run</th>
                                    <th>state</th>
                                    <th>log</th>
                                </tr>
                            </thead>

                            <tbody id="#tests-table">
                                {testNodes}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        );
    }
});

// ------------------------------------------------
// ---- TestRow

var TestRow = React.createClass({
    toogleOne: function(event, whatever)
    {
        jQuery.ajax({
            url: '/ci-watcher/tests/enable/'+event.target.checked+'/'+this.props.projectId+'/'+this.props.test.id,

            dataType: 'json',

            error: function(xhr, status, err)
            {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    checkboxChanged: function()
    {
        /// ignore this
    },

    render: function()
    {
        return (
            <tr key={this.props.test.id} className={ ! this.props.test.enabled ? 'dim' : ''}>
                <td>
                    <input
                        type="checkbox"
                        title="Mark to enable test"
                        className="testCheckbox"
                        onClick={this.toogleOne}
                        onChange={this.checkboxChanged}
                        checked={this.props.test.enabled}
                    />
                </td>
                <td><RunButton test={this.props.test} /></td>
                <td>{this.props.test.name}</td>
                <td>{this.props.test.updated_at}</td>
                <td><State type={this.props.test.state} /></td>
                <td>
                    <LogButton
                        test={this.props.test}
                        type={this.props.test.state}
                        log={this.props.test.log}
                        name={this.props.test.name}
                        html={this.props.test.html}
                        image={this.props.test.image}
                    />
                </td>
            </tr>
        );
    }
});
