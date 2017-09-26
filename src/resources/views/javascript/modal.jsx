// ------------------------------------------------
// ---- Modal

var BootstrapModal = React.createClass({
    // The following two methods are the only places we need to
    // integrate with Bootstrap or jQuery!
    componentDidMount: function()
    {
        // When the component is added, turn it into a modal
        jQuery(this.getDOMNode())
            .modal({backdrop: 'static', keyboard: false, show: false})
    },

    getInitialState: function()
    {
        return {
            selectedPanel: 'log',
        };
    },

    componentWillUnmount: function()
    {
        jQuery(this.getDOMNode()).off('hidden', this.handleHidden);
    },

    close: function()
    {
        jQuery(this.getDOMNode()).modal('hide');
    },

    open: function()
    {
        jQuery(this.getDOMNode()).modal('show');
    },

    render: function()
    {
        var confirmButton = null;

        var cancelButton = null;

        if (this.props.confirm) {
            confirmButton = (
                <BootstrapButton
                    onClick={this.handleConfirm}
                    className="btn-success"
                >
                    {this.props.confirm}
                </BootstrapButton>
            );
        }

        if (this.props.cancel) {
            cancelButton = (
                <BootstrapButton
                    onClick={this.handleCancel}
                    className="btn-default"
                    dataKeyboard="true"
                >
                    {this.props.cancel}
                </BootstrapButton>
            );
        }

        var tabStyle = {
            fontFamily: 'Courier New',
            maxHeight: 'calc(100vh - 280px)',
            overflowY: 'auto',
            overflowX: 'auto',
            whiteSpace: 'pre',
            padding: '10px',
        };

        var tabLogStyle = {
            backgroundColor: 'black',
            color: 'white',
        };

        jQuery.extend( tabLogStyle, tabStyle );

        var modalDialogStyle = {
            width: '90%',
        };

        return (
            <div className="modal fade" tabIndex="-1">
                <div className="modal-dialog modal-lg" style={modalDialogStyle}>
                    <div className="modal-content">
                        <div className="modal-header">
                            <button
                                type="button"
                                className="close"
                                dataDismiss="modal"
                                onClick={this.handleCancel}>
                                &times;
                            </button>
                            <h3>{this.props.title}</h3>
                        </div>

                        <div className="modal-body">
                            <div className="btn btn-pill btn-primary" onClick={this.setPanelLog}>
                                command output
                            </div>
                            &nbsp;
                            <div className="btn btn-pill btn-outline-primary" onClick={this.setPanelScreenshot}>
                                screenshot
                            </div>
                            &nbsp;
                            <div className="btn btn-pill btn-outline-primary" onClick={this.setPanelHtml}>
                                html
                            </div>
                            &nbsp;
                            <div className="btn btn-pill btn-outline-primary">
                                { this.state.selectedPanel }
                            </div>

                            <br/>
                            {/*<ul id="tabs" className="nav nav-tabs" data-tabs="tabs">*/}
                                {/*<li className="active"><a href={"#tab-log-"+this.props.test.id} data-toggle="tab">Command Output</a></li>*/}
                                {/*<li><a href={"#tab-screenshot-"+this.props.test.id} data-toggle="tab">Screenshot</a></li>*/}
                                {/*<li><a href={"#tab-html-"+this.props.test.id} data-toggle="tab">HTML</a></li>*/}
                            {/*</ul>*/}

                            <div id="my-tab-content" className="tab-content">
                                <div className="tab-pane active" id={"tab-log-"+this.props.test.id}>
                                    <div style={tabLogStyle}>
                                        {this.props.children}
                                    </div>
                                </div>
                                <div className="tab-pane" id={"tab-screenshot-"+this.props.test.id}>
                                    <div style={tabStyle}>
                                        <img src={this.props.image} alt=""/>
                                    </div>
                                </div>
                                <div className="tab-pane" id={"tab-html-"+this.props.test.id}>
                                    <div style={tabStyle}>
                                        {this.props.html}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="modal-footer">
                            {cancelButton}
                            {confirmButton}
                        </div>
                    </div>
                </div>
            </div>
        );
    },

    handleCancel: function() {
        if (this.props.onCancel) {
            this.props.onCancel();
        }
    },

    handleConfirm: function() {
        if (this.props.onConfirm) {
            this.props.onConfirm();
        }
    },

    openFile: function() {
        console.log('openfile');
    },

    setPanelLog: function() {
        this.state.selectedPanel = 'log';
    },

    setPanelScreenshot: function() {
        this.state.selected = 'screenshot';
    },

    setPanelHtml: function() {
        this.state.selected = 'html';
    },
});
