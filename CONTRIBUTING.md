# Contributing

Any and all are encouraged to contribute! There are three primary ways to do so: creating issues, submitting pull requests (PRs), and reviewing issues and PRs.

## Issue Guidelines

* The title should be a simple declarative sentence; examples,
    * Good: Symfony code style documentation is missing.
    * Bad: Add Symfony code style documentation. [bad because it is imperative]
    * Bad: What is the Symfony code style documentaiton? [bad because it is interrogative]
    * Bad: Symfony and Drupal code style documentation are missing. [bad because it is compound]
* The issue should be atomic: the issue should address an indivisible and irreducible topic.
    * Good: Symfony code style documentation is missing.
    * Bad: Code style documentation is missing. [bad because it could be divided into Drupal and Symfony documentation]
    * Bad: Symfony best practice documentation is missing. [bad if only code style documentation was missing because it could be reduced to just Symfony code style documentation]
* The issue description should:
    * give context: explain why you are writing the issue
    * state the problem or idea: the context should lead into what needs changed or added to the standards
    * identify the next step: e.g. request feedback, assign the issue to someone, or further investigation required (this is required! this is how the community knows how to respond)
* Open an issue when you have a clear idea of a problem that needs addressed (e.g. missing documentation), but do not have a clear idea of the solution.
* Close an issue if it is stale and no longer relevant.
* If an issue was created that is a duplicate, close the issue that has the least activity and make sure they reference one another.

## Pull Request Guidelines

* The title should be a simple imperative sentence; examples,
    * Good: Add Symfony code style documentation.
    * Bad: Adding Symfony code style documentation. [bad because it is declarative]
    * Bad: Is this good Symfony code style documentation? [bad because it is interrogative]
    * Bad: Add Symfony and Drupal code style documentation. [bad because it is compound]
* The PR should be atomic: the PR should address an indivisible and irreducible topic. The more atomic PRs are the more easily they can be reviewed and merged.
    * Do not address multiple issues in one PR.
* The PR description should:
    * give context: explain why you are creating the PR, e.g. reference the GitHub issue number it addresses
    * state the solution: the context should lead into what you did to resolve the issue
    * identify any follow up changes that will be needed and how that is going to be addressed, i.e. what does this PR not address?
* If you see an issue with the documentation and see a solution, please take responsibility to fix it and open a PR.
* If you are unsure of the solution, please open an issue.
* Do not merge a PR unless there is consensus and approval from a Senior Engineer, Senior Front End Developer, Development Manager.
* If a PR is stale and no longer relevant, please review it with a Senior Engineer, Senior Front End Developer, or Development Manager to confirm closing the PR. You or the person may close the PR.
* If a PR is created to solve a problem that duplicates another issue, the community should decide which to keep.
* Avoid placeholder documentation. If documentation is missing, please create an issue (see Issue Guidelines).
* If you create a branch you own it; no one else should modify it without your permission.
* If you wish to make a change to someone else's PR, either suggest it in a comment or ask the PR owner to meet with you for a pair programming session/discussion. Talking first can solve the problem sooner and increase knowledge sharing which is important to our team.
* Since this is a documentation repository, significant consideration should be given to grammar, phraseology, accuracy, and structure of the information being documented.

## Git Commit Guidelines

Commits should be atomic: the commit should be an indivisible and irreducible change. The commit message can be best described with an example, which we will model after Tim Pope's [blog post](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html):

    Capitalized, short (50 chars or less) summary.

    More detailed explanatory text, if necessary. Wrap it to 72
    characters. Think of the first line as a subject of an email and
    the rest as the body. The blank line separating the summary from
    the body is critical (unless you omit the body entirely); tools
    like rebase can get confused if you run the two together.

    Write your commit message in the imperative: "Fix bug" and not "Fixed bug"
    or "Fixes bug." This convention matches up with commit messages generated
    by commands like git merge and git revert.

    Further paragraphs come after blank lines.

    - Bullet points are okay, too

    - Typically a hyphen or asterisk is used for the bullet, followed by a
      single space, with blank lines in between, but conventions vary here

    - Use a hanging indent

## Community Guidelines

Development standards are the collective technical values and goals shared by all Palantiri. Our hope and expectation is that everyone, no matter level of skill or experience, will feel welcome to own these standards. As owners, we hope you both challenge existing standards and establish new ones. Here are goals that we hope our community seeks to promote in our standards:

* **Integrity** - our standards should reflect the level of excellence we want to embody in practice.
* **Dialectic** - our conversations around standards should be a practice of dialectic rather than debate.
* **Efficiency** - our standards and conversations should be brief, factual, and to the point.
* **Completeness** - documentation of our standards should be concise, but comprehensive--explore and document edge cases.
* **Atomicity** - changes to standards should be the smallest set of coherent changes.
