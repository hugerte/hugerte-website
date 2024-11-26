## DEPLOYMENT.md

**Deployment Process**

This document outlines the deployment process for this project.

**Deployment Tool**

This project leverages [DeployHQ](https://www.deployhq.com/) for automated deployments. DeployHQ is a robust and user-friendly platform that streamlines the deployment workflow.

**Open Source License**

It is important to acknowledge that we are utilising the DeployHQ Open Source license for this deployment. This license grants us access to the core functionalities of DeployHQ that are essential for our deployment needs.

**Deployment Steps**

1. DeployHQ gets notified about new commits via GitHub Webhooks. The website is then deployed automatically.
2. DeployHQ runs `composer install --no-progress --no-dev --no-interaction` and `npm install` to install the required dependencies.
3. DeployHQ pushes the changes to the server via SSH/SFTP.

**Benefits of Using DeployHQ**

-   **Automation:** DeployHQ automates the deployment process, reducing manual intervention and the risk of errors.
-   **Efficiency:** Deployments can be triggered automatically or manually, streamlining the workflow.
-   **Reliability:** DeployHQ offers a reliable platform to ensure consistent and successful deployments.
-   **Open Source License:** The Open Source license provides a cost-effective solution for our deployment needs.

**Disclaimer**

The information contained in this document is for informational purposes only and may be subject to change. Please refer to the official [DeployHQ](https://www.deployhq.com/) documentation if you want to deploy a project yourself.
