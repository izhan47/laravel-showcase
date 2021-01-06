<footer class="footer d-none">
    <div class="container-fluid">
        <div class="row text-muted">
            <div class="col-6 text-left">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a class="text-muted" href="#">Support</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-muted" href="#">Help Center</a>
                    </li>
                </ul>
            </div>
            <div class="col-6 text-right">
                <p class="mb-0">
                    &copy; {{date('Y')}} - <a href="{{url("/admin")}}" class="text-muted">{{ config("app.name") }}</a>
                </p>
            </div>
        </div>
    </div>
</footer>