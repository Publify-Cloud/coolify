<?php

it('preserves Publify fork branding and production image references', function () {
    $projectRoot = dirname(__DIR__, 2);

    expect(file_get_contents($projectRoot.'/app/Models/S3Storage.php'))
        ->toContain("subject('Publify: S3 Storage Connection Error')")
        ->not->toContain("subject('Coolify: S3 Storage Connection Error')")
        ->and(file_get_contents($projectRoot.'/docker-compose.prod.yml'))
        ->toContain('image: "${REGISTRY_URL}/publify:${LATEST_IMAGE:-latest}"')
        ->and(file_get_contents($projectRoot.'/other/nightly/docker-compose.prod.yml'))
        ->toContain('image: "${REGISTRY_URL}/publify:${LATEST_IMAGE:-latest}"');
});

it('preserves Publify stable and nightly install and upgrade configuration', function () {
    $projectRoot = dirname(__DIR__, 2);

    foreach (['scripts/install.sh', 'other/nightly/install.sh'] as $path) {
        expect(file_get_contents($projectRoot.'/'.$path))
            ->toContain('CDN="https://cdn.publify.justahost.cloud"')
            ->toContain('REGISTRY_URL="public.ecr.aws/g6l4g2t4"')
            ->toContain('Publify Installation')
            ->not->toContain('Coolify Installation');
    }

    foreach (['scripts/upgrade.sh', 'other/nightly/upgrade.sh'] as $path) {
        expect(file_get_contents($projectRoot.'/'.$path))
            ->toContain('CDN="https://cdn.publify.justahost.cloud"')
            ->toContain('REGISTRY_URL="public.ecr.aws/g6l4g2t4"')
            ->toContain('ghcr.io/coollabsio/coolify-helper')
            ->not->toContain('${REGISTRY_URL:-docker.io}/coollabsio/coolify-helper')
            ->toContain('Publify Upgrade')
            ->not->toContain('Coolify Upgrade');
    }

    foreach (['scripts/upgrade-postgres.sh', 'other/nightly/upgrade-postgres.sh'] as $path) {
        expect(file_get_contents($projectRoot.'/'.$path))
            ->toContain('Publify internal PostgreSQL')
            ->not->toContain('Coolify internal PostgreSQL');
    }

    expect(file_get_contents($projectRoot.'/README.md'))
        ->toContain('curl -fsSL https://cdn.publify.justahost.cloud/install.sh | bash')
        ->not->toContain('curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash')
        ->and(file_get_contents($projectRoot.'/other/nightly/.env.production'))
        ->toContain('APP_NAME=Coolify')
        ->toContain('REGISTRY_URL=public.ecr.aws/g6l4g2t4')
        ->and(file_get_contents($projectRoot.'/other/nightly/docker-compose.prod.yml'))
        ->toContain("image: 'ghcr.io/coollabsio/coolify-realtime:1.0.16'")
        ->not->toContain("image: '\${REGISTRY_URL:-docker.io}/coollabsio/coolify-realtime:1.0.16'")
        ->and(file_get_contents($projectRoot.'/resources/views/layouts/base.blade.php'))
        ->toContain('https://cdn.publify.justahost.cloud/assets/og-image.png')
        ->toContain("config('app.name') == 'Coolify Cloud'")
        ->and(file_get_contents($projectRoot.'/resources/views/livewire/settings/index.blade.php'))
        ->toContain('placeholder="https://publify.yourdomain.com"')
        ->not->toContain('placeholder="https://coolify.yourdomain.com"');

    expect(config('constants.coolify.versions_url'))
        ->toBe('https://cdn.publify.justahost.cloud/versions.json')
        ->and(config('constants.coolify.upgrade_script_url'))
        ->toBe('https://cdn.publify.justahost.cloud/upgrade.sh')
        ->and(config('constants.coolify.helper_image'))
        ->toBe('ghcr.io/coollabsio/coolify-helper')
        ->and(config('constants.coolify.realtime_image'))
        ->toBe('ghcr.io/coollabsio/coolify-realtime');
});
