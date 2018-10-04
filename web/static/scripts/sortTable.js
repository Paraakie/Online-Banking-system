/**
 * Sorts an html table body
 * @param tableBody The body of the html table to sort
 * @param col The index of the column to sort by
 * @param order 1 = ascending order, -1 = descending order
 */

function sortTable(tableBody, col, order)
{
    const rows = tableBody.rows;
    const rowsLength = rows.length;
    let arr = [];
    // fill the array with values from the table
    for(let i = 0; i < rowsLength; i++)
    {
        const cells = rows[i].cells;
        const cellsLength = cells.length;
        arr[i] = [];
        for(let j = 0; j < cellsLength; j++)
        {
            arr[i][j] = cells[j].innerHTML;
        }
    }
    // sort the array by the specified column number (col) and order (order)
    arr.sort(function(a, b)
    {
        let ordering = 0;
        let fA = parseFloat(a[col]);
        let fB = parseFloat(b[col]);
        if(a[col] != b[col])
        {
            if((fA==a[col]) && (fB==b[col]) ){ ordering=( fA > fB ) ? order : -order; } //numerical
            else { ordering=(a[col] > b[col]) ? order : -order;} //lexical
        }
        return ordering;
    });
    for(let row=0; row < rowsLength; row++)
    {
        for(let column=0; column < arr[row].length; column++)
        {
            tableBody.rows[row].cells[column].innerHTML=arr[row][column];
        }
    }
}