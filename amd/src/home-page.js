export const init = () => {
    let elem = document.getElementById('format-menutab-close-editing-help');
    let courseId = document.getElementById('format-menutab-courseid').value;
    let userId = document.getElementById('format-menutab-userid').value;
    let sessionItemVariable = userId + '-' + courseId + '-EditingHelp';
    let hideEditingHelp = sessionStorage.getItem(sessionItemVariable);
    // Hide element if session variable is set
    if (hideEditingHelp === 'true') {
        let container = document.getElementById('in-edit-mode-container');
        container.style.display = 'none';
    }

    elem.addEventListener('click', () => {
        let container = document.getElementById('in-edit-mode-container');
        container.style.display = 'none';
        sessionStorage.setItem(sessionItemVariable, true);
    });
};