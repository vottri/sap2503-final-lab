pipeline {
    agent any

    environment {
        DOCKER_REGISTRY   = "docker.io"
        IMAGE_NAME        = "vothangtri/student-app"
        GIT_CREDENTIAL   = "git-cred"
        DOCKER_CREDENTIAL = "docker-cred"
    }

    stages {

        stage('Checkout') {
            steps {
                git credentialsId: "${GIT_CREDENTIAL}",
                    url: "https://github.com/vottri/sap2503-final-lab.git",
                    branch: "main"
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    IMAGE_TAG = "build-${env.BUILD_NUMBER}"

                    sh """
                    docker build \
                      -t ${IMAGE_NAME}:${IMAGE_TAG} \
                      .
                    """
                }
            }
        }

        stage('Push Docker Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: "${DOCKER_CREDENTIAL}",
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASS'
                    )
                ]) {
                    sh """
                    echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin ${DOCKER_REGISTRY}
                    docker push ${IMAGE_NAME}:${IMAGE_TAG}
                    """
                }
            }
        }

        stage('Update Helm values.yaml') {
            steps {
                sh """
                sed -i 's/tag:.*/tag: "${IMAGE_TAG}"/' charts/student-app/values.yaml
                """
            }
        }

        stage('Commit & Push Back') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: "${GIT_CREDENTIAL}",
                        usernameVariable: 'GIT_USER',
                        passwordVariable: 'GIT_PASS'
                    )
                ]) {
                    sh """
                    git config user.email "thangtrivo1991@gmail.com"
                    git config user.name "vottri"

                    git add charts/student-app/values.yaml
                    git commit -m "ci: update student-app image to ${IMAGE_TAG}" || echo "No changes to commit"
                    git push https://${GIT_USER}:${GIT_PASS}@github.com/vottri/sap2503-final-lab.git HEAD:main
                    """
                }
            }
        }
    }
}
