HTMLTableElement.prototype.editada=false;
HTMLTableElement.prototype.noEditable=[];

HTMLTableElement.prototype.saluda=function(){
    alert("Hola");
}

HTMLTableElement.prototype.getData=function(){
    var tBody=this.tBodies[0];
    let datos=[];
    let filas=tBody.rows;
    let nFilas=filas.length;
    for (let i=0;i<nFilas;i++){
        let fila=[];
        let nColumnas=filas[i].cells.length;
        for (let j=0;j<nColumnas;j++){
            if (!filas[i].cells[j].classList.contains("editada"))
            fila.push(filas[i].cells[j].innerHTML);
        }
        datos.push(fila); 
    }
    return datos;
}

HTMLTableElement.prototype.setData=function(datos){
    var tBody=this.tBodies[0];
    tBody.innerHTML="";
    let nFilas=datos.length;
    for (let i=0;i<nFilas;i++){
        let fila=document.createElement("tr");
        let nColumnas=datos[i].length;
        for (let j=0;j<nColumnas;j++){
            let celda=document.createElement("td");
            celda.innerHTML=datos[i][j];
            fila.appendChild(celda);
        }
        tBody.appendChild(fila);
    }
}
HTMLTableElement.prototype.desactivarEdicion=function(){
    if(this.editada){
        this.editada=false;
        var tBody=this.tBodies[0];
        var tHead=this.tHead;
        let tFoot=this.tFoot;
        let filas=tBody.rows;
        let nfilas=filas.length;
        let columnas=this.tHead.rows[0].cells.length;
        tHead.rows[0].removeChild(this.tHead.rows[0].cells[columnas-1])
        for(let i=0;i<nfilas;i++){
            filas[i].cancelar();
            filas[i].removeChild(filas[i].cells[columnas-1]);
        }
        let filaInserccion=tFoot.children[0];
        tFoot.removeChild(filaInserccion);
    }
}

HTMLTableElement.prototype.activarEdicion=function(callBackBorrar,callBackEditar,callBackGuardar,callBackCancelar){
    if (!this.editada){
        this.editada=true;
        if (this.dataset.noeditable){
            this.noEditable=JSON.parse(this.dataset.noeditable);
        }
        let tBody=this.tBodies[0];
        let tFoot=this.tFoot;
        let filas=tBody.rows;
        let nfilas=filas.length;
        let columnas=this.tHead.rows[0].cells.length;
        let td=document.createElement("td");
        td.innerHTML="ACCIONES";
        this.noEditable.push(columnas);
        this.noEditable.sort(function(a,b){a-b});
        this.tHead.rows[0].appendChild(td);
        for (let i=0;i<nfilas;i++){
            crearCeldaBotones(filas[i],callBackBorrar,callBackEditar,callBackGuardar,callBackCancelar);         
        }
        let tr=document.createElement("tr");
        tr.classList.add("editada");
        if (this.tFoot){
            if (this.tFoot.rows.length==0){
                this.tFoot.appendChild(tr);
            }else{
                this.tFoot.insertBefore(tr,this.tFoot.rows[0]);
            }
        }else{
            let tfoot=document.createElement("tfoot");
            tfoot.appendChild(tr);
            this.appendChild(tfoot);
        }
        
        for (let i=0;i<columnas;i++){
            let input=document.createElement("input");
            input.type="text";
            let td=document.createElement("td");
            td.appendChild(input);
            tr.appendChild(td);
        }
        let inputs=tr.getElementsByTagName("input");
        for (let i=0;i<inputs.length-1;i++){
            inputs[i].addEventListener("keypress",function(ev){
                if (ev.key=="Enter" && this.value!=""){
                    this.parentElement.nextSibling.children[0].focus();
                }
            })
        }
        inputs[inputs.length-1].addEventListener("keypress",function(ev){
            if (ev.key=="Enter"&& this.value!=""){
                this.parentElement.nextSibling.children[0].click();
            }
        })

        let td1=document.createElement("td");
        let span3=document.createElement("span");
        span3.innerHTML="";
        span3.className="botonGuardar";
        span3.onclick=function(){
            let inputs=this.parentElement.parentElement.getElementsByTagName("input");
            let tr=document.createElement("tr");
            tBody.appendChild(tr);
            for (let i=0;i<inputs.length;i++){
                let td=document.createElement("td");
                td.innerHTML=inputs[i].value;
                tr.append(td);
            }
            crearCeldaBotones(tr,callBackBorrar,callBackEditar,callBackGuardar,callBackCancelar);
            this.nextSibling.click();

        };
        let span4=document.createElement("span");
        span4.innerHTML="";
        span4.className="botonCancelar";
        span4.onclick=function(){
            let inputs=this.parentElement.parentElement.getElementsByTagName("input");
            for (let i=0;i<inputs.length;i++){
                inputs[i].value="";
            }
            inputs[0].focus();
        }
        td1.appendChild(span3);
        td1.appendChild(span4);
        tr.appendChild(td1);
    }
}


HTMLTableRowElement.prototype.editar=function(){
    let celdas=this.cells;
    let nceldas=celdas.length;
    let noEditable=this.parentElement.parentElement.noEditable;
    this.classList.add("editada");
    for (let i=0;i<nceldas;i++){
        if(noEditable.indexOf(i)==-1){
          let valor=celdas[i].innerHTML;
          celdas[i].innerHTML="";
          let input=document.createElement("input");
          input.value=valor;
          input.original=valor;
          celdas[i].appendChild(input); 
        }
    }
}

HTMLTableRowElement.prototype.guardar=function(){
    let inputs=this.getElementsByTagName("input");
    //let nInputs=inputs.length;
    this.classList.remove("editada");
    /*for (let i=0;i<nInputs;i++){
          let valor=inputs[0].value;
          inputs[0].parentElement.innerHTML=valor;
    }*/
    while (inputs.length>0){
        let valor=inputs[0].value;
        inputs[0].parentElement.innerHTML=valor;
    }
}

HTMLTableRowElement.prototype.cancelar=function(){
    let inputs=this.getElementsByTagName("input");
    //let nInputs=inputs.length;
    this.classList.remove("editada");
    /*for (let i=0;i<nInputs;i++){
          let valor=inputs[0].value;
          inputs[0].parentElement.innerHTML=valor;
    }*/
    while (inputs.length>0){
        let valor=inputs[0].original;
        inputs[0].parentElement.innerHTML=valor;
    }
}


function crearCeldaBotones(fila, callBackBorrar, callBackEditar, callBackGuardar, callBackCancelar) {
    let td = document.createElement("td");
    let botonBorrar = document.createElement("button");
    botonBorrar.innerHTML = "";
    botonBorrar.className = "botonBorrar";
    botonBorrar.onclick = callBackBorrar(fila);

    let botonEditar = document.createElement("button");
    botonEditar.innerHTML = "";
    botonEditar.className = "botonEditar";
    botonEditar.onclick = callBackEditar(fila);

    let botonGuardar = document.createElement("button");
    botonGuardar.innerHTML = "";
    botonGuardar.className = "botonGuardar";
    botonGuardar.onclick = callBackGuardar(fila);

    let botonCancelar = document.createElement("button");
    botonCancelar.innerHTML = "";
    botonCancelar.className = "botonCancelar";
    botonCancelar.onclick = callBackCancelar(fila);

    td.appendChild(botonBorrar);
    td.appendChild(botonEditar);
    td.appendChild(botonGuardar);
    td.appendChild(botonCancelar);
    fila.appendChild(td);
}