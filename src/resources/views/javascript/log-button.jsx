// ------------------------------------------------
// ---- Log Button

var LogButton = React.createClass({
    render: function()
    {
        if (this.props.type == 'failed')
        {
            var modal = null;

            body = React.DOM.div({ dangerouslySetInnerHTML:
                {
                    __html: this.props.log
                }});

            modal = (
                <BootstrapModal
                    ref="modal"
                    confirm="Close"
                    onConfirm={this.closeModal}
                    onCancel={this.closeModal}
                    title={this.props.name}
                    html={this.props.html}
                    image={this.props.image}
                    test={this.props.test}
                >
                    {body}
                </BootstrapModal>
            );

            return (
                <div className="example">
                    {modal}

                    <button
                        type="button"
                        className="btn btn-sm btn-primary"
                        onClick={this.openModal}
                        data-keyboard="true"
                    >
                        Show
                    </button>
                </div>
            );
        }

        return false;
    },

    openModal: function()
    {
        this.refs.modal.open();
    },

    closeModal: function()
    {
        this.refs.modal.close();
    }
});
