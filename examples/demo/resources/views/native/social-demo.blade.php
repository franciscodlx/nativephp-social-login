<native:scroll-view class="w-full h-full">
    <native:column class="w-full gap-3 p-4 safe-area">
        <native:text class="text-2xl font-bold">Social login · NativePHP</native:text>
        <native:text class="text-sm text-zinc-500">Providers without a client ID in .env use the local mocks.</native:text>

        @foreach ($providers as $provider)
            <native:button
                class="w-full"
                label="Sign in with {{ $provider['label'] }}"
                :disabled="$status === 'working'"
                @tap="signIn('{{ $provider['id'] }}')"
            />
        @endforeach

        @if ($status === 'working')
            <native:activity-indicator />
        @elseif ($status === 'failed')
            <native:text class="text-red-700 font-bold">{{ $message }}</native:text>
        @elseif ($status === 'done')
            <native:column class="gap-1 p-3 bg-zinc-100 rounded-lg">
                <native:text class="text-green-700 font-bold">SIGNED IN ({{ $result['provider'] }})</native:text>
                <native:text class="font-mono">id: {{ $result['id'] }}</native:text>
                <native:text class="font-mono">name: {{ $result['name'] }}</native:text>
                <native:text class="font-mono">username: {{ $result['username'] }}</native:text>
                <native:text class="font-mono">email: {{ $result['email'] }}</native:text>
            </native:column>
        @endif
    </native:column>
</native:scroll-view>
