export const init = () => {
    let elem = document.getElementById('format-menutab-close-editing-help');
    let courseId = document.getElementById('format-menutab-courseid').value;
    let sessionItemVariable =courseId + '-EditingHelp';
    let hideEditingHelp = sessionStorage.getItem(sessionItemVariable);
    console.log("hideEditingHelp: " + hideEditingHelp);
    // Hide element if session variable is set
    if (hideEditingHelp == 'true') {
        let container = document.getElementById('in-edit-mode-container');
        container.style.display = 'none';
    }

    elem.addEventListener('click', () => {
        let container = document.getElementById('in-edit-mode-container');
        container.style.display = 'none';
        sessionStorage.setItem(sessionItemVariable, true);
    });
};