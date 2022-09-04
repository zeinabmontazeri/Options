# Git Branch Convention

- ### _Branch Name_
    - The branch name should be in the following format:
        - First Part:
            - `feat/`: If the branch is a feature branch
            - `fix/`: If the branch is a fix branch
            - `docs/`: If the branch is a documentation branch
        - Second Part:
            - `back/`: If you works on the back-end
            - `front/`: If you works on the front-end
        - Third Part
            - `# {int}/`: Check trello board for the ticket number
        - Fourth Part
            - `{string}/`: Check trello board for the ticket title and write it down in  
              snake_case.
        - ex: `fix/back/#10/get_something_something`

- ### _Commit Message_
    - The commit message should be in the following format:
        - ex: `feat(back): #10 a brief description of what you've done`
        - ex: `fix(back): #10 a brief description of what you've done`

### 🚨 For peace of mind after merging your branch with `dev`, please delete your branch and run 
`git fetch --prune`. :))
