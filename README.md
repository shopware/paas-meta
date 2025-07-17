## shopware/paas-meta

This meta-package for the Shopware ecosystem focuses on supporting Shopware's [Platform-as-a-Service](https://www.shopware.com/en/shopware-paas/) (PaaS) offering.


### Purpose and Role

As a meta package, **paas-meta** serves as a collection of dependencies and recipe definitions required for running Shopware on P.SH  PaaS environments. It centralizes configuration and platform-specific adjustments needed for smooth deployment and operations, and orchestrates the setup and environment expectations for Shopware running as a service on managed infrastructure. It does not contain application logic itself.

### Key Features

- **Brings Together Essentials**: Pulls in required components via Composer, including Shopware core, deployment helpers, and integration packages necessary for PaaS use cases.
- **Configuration Recipes**: Provides and updates deployment recipes, including best practices and configuration templates (such as handling environment variables, mounts, deployment hooks) needed for Shopware in PaaS.
- **Supports Automated Environments**: Serves as the foundation for setting up Shopware stores on cloud platforms where code, infrastructure, and deployment need to be standardized and automated.


### Typical Use

- **As a Dependency**: Add the `shopware/paas-meta` package to a Shopware project (typically using Composer). This ensures all PaaS-related dependencies and configurations are present.
- **Build & Deploy Pipeline**: Every push to a repository using this package triggers automated builds and deployments in the cloud PaaS environment, following the configuration provided by this meta-package ([see also](https://developer.shopware.com/docs/products/paas/shopware-paas/build-deploy.html)).

### Who Should Use It

- Developers or merchants who want to deploy Shopware on a cloud platform (as a service) and need a standardized, officially-supported setup.
- Teams aiming to avoid manual, error-prone configuration.

For full documentation and integration instructions, refer to [Shopware PaaS Documentation](https://github.com/shopware/paas-meta).