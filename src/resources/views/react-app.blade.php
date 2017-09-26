<script type="text/jsx">
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/run-button.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/button.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/event-system.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/log-button.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/modal.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/projects.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/state.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/test-table.jsx'); !!}
    {!! file_get_contents(CI_PATH.'/src/resources/views/javascript/file-url.jsx'); !!}

    // ------------------------------------------------
    // ---- Render the app

    {{--React.render(--}}
            {{--<TestsTable url={"/ci-watcher/tests/"} pollInterval={2000}/>,--}}
        {{--document.getElementById('table-container')--}}
    {{--);--}}

//    React.render(
//            <ProjectsMenu url="/ci-watcher/projects"/>,
//        document.getElementById('projects')
//    );
</script>
