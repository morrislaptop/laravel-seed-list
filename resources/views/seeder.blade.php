<li>
    {{ str_replace(config('seed-list.seeders_namespace'), '', $seeder['name']) }}
    @if (count($seeder['children']) > 0)
    <ul>
        @foreach ($seeder['children'] as $seeder)
            @include('seed-list::seeder', $seeder)
        @endforeach
    </ul>
    @endif
</li>
