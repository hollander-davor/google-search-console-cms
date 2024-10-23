<script>
    function checkNewQueriesFirst() {
        $.ajax({
            url: "{{ route('google_search_console_user.check_new_queries') }}",
            method: "GET"

        }).done(function(response) {
            $.each(response.data, function(index, value) {
                if (index == response.data.length - 1) {
                    var newItem = '<li class="user-box list-inline-item seo-gsc">' +
                        '<a href="/google-search-console-user/' + value.site_id +
                        '" class="user-link" data-toggle="tooltip" data-placement="bottom" title="">' +
                        '<div class="row">' +
                        '<span style="color: white; margin-right: 10px;">SEO</span>' +
                        '<div class="text-center" style="width:auto;height:36px; background-color: red;padding-left:5px;padding-right:5px;">' +
                        '<p style="color: white; padding-top: 5px;">' + value.name + ' (' + value
                        .count +
                        ')</p>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                } else {
                    var newItem = '<li class="user-box list-inline-item seo-gsc">' +
                        '<a href="/google-search-console-user/' + value.site_id +
                        '" class="user-link" data-toggle="tooltip" data-placement="bottom" title="">' +
                        '<div class="row">' +
                        '<div class="text-center" style="width:auto;height:36px; background-color: red;padding-left:5px;padding-right:5px;">' +
                        '<p style="color: white; padding-top: 5px;">' + value.name + ' (' + value
                        .count +
                        ')</p>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                }
                $('#new_queries_button').prepend(newItem);
            });

        }).fail(function(xhr) {});
    }

    function checkNewQueries(timeInterval = false) {
        let interval = timeInterval ? timeInterval * 1000 : parseInt("{{ config('gsc-cms.ajax_interval') }}") * 1000;
        setInterval(function() {
            $.ajax({
                url: "{{ route('google_search_console_user.check_new_queries') }}",
                method: "GET"

            }).done(function(response) {
                $('li.seo-gsc').each(function() {
                    $(this).remove();
                });

                $.each(response.data, function(index, value) {
                    if (index == response.data.length - 1) {
                        var newItem = '<li class="user-box list-inline-item seo-gsc">' +
                            '<a href="/google-search-console-user/' + value.site_id +
                            '" class="user-link" data-toggle="tooltip" data-placement="bottom" title="">' +
                            '<div class="row">' +
                            '<span style="color: white; margin-right: 10px;">SEO</span>' +
                            '<div class="text-center" style="width:auto;height:36px; background-color: red;padding-left:5px;padding-right:5px;">' +
                            '<p style="color: white; padding-top: 5px;">' + value.name + ' (' +
                            value.count +
                            ')</p>' +
                            '</div>' +
                            '</div>' +
                            '</a>' +
                            '</li>';
                    } else {
                        var newItem = '<li class="user-box list-inline-item seo-gsc">' +
                            '<a href="/google-search-console-user/' + value.site_id +
                            '" class="user-link" data-toggle="tooltip" data-placement="bottom" title="">' +
                            '<div class="row">' +
                            '<div class="text-center" style="width:auto;height:36px; background-color: red;padding-left:5px;padding-right:5px;">' +
                            '<p style="color: white; padding-top: 5px;">' + value.name + ' (' +
                            value.count +
                            ')</p>' +
                            '</div>' +
                            '</div>' +
                            '</a>' +
                            '</li>';
                    }
                    $('#new_queries_button').prepend(newItem);
                });

            }).fail(function(xhr) {});
        }, interval);
    }

    $(document).ready(function() {
        @can('gsc-user')
            checkNewQueriesFirst();
            checkNewQueries();
        @endcan
    });
</script>
