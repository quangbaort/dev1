export const  makeIcsFile = (event) => {
  var data = new File([event], { type: "text/plain" });
  return window.URL.createObjectURL(data);
}
